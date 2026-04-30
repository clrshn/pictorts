<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\SavedFilter;
use App\Services\ActivityLogService;
use App\Services\InAppNotificationService;
use App\Support\TableExport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    private function ensureTodoActionAllowed(Todo $todo, bool $expectsJson = false, string $action = 'update')
    {
        // Task approval locks follow the same rule set as other modules so the
        // collaboration workflow behaves consistently across the application.
        $approval = $todo->approval;

        if (!auth()->user()?->isAdmin() && $approval?->status === 'pending') {
            return $expectsJson
                ? response()->json(['success' => false, 'message' => 'This task has a pending approval request and cannot be changed right now.'], 422)
                : redirect()->back()->with('warning', 'This task has a pending approval request and cannot be changed right now.');
        }

        if (!auth()->user()?->isAdmin() && in_array($action, ['update', 'delete'], true) && $approval?->status === 'approved') {
            return $expectsJson
                ? response()->json(['success' => false, 'message' => 'This task is already approved. Only an admin can modify it now.'], 422)
                : redirect()->back()->with('warning', 'This task is already approved. Only an admin can modify it now.');
        }

        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // The task listing also powers export/report output. Keeping the query logic
        // centralized prevents filter mismatches between UI tables and generated reports.
        $query = Todo::query();
        $exportMode = $request->get('export');

        // Filter by status
        if ($request->filled('status') && $request->status !== 'ALL') {
            $query->byStatus($request->status);
        }

        // Filter by priority
        if ($request->filled('priority') && $request->priority !== 'ALL') {
            $query->byPriority($request->priority);
        }

        // Filter by assigned person
        if ($request->filled('assigned_to') && $request->assigned_to !== '') {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('due_alert')) {
            $today = Carbon::today();
            $query->whereNotIn('status', ['done', 'cancelled'])
                ->whereNotNull('due_date');

            match ($request->due_alert) {
                'overdue' => $query->whereDate('due_date', '<', $today),
                'today' => $query->whereDate('due_date', $today),
                'tomorrow' => $query->whereDate('due_date', Carbon::tomorrow()),
                'soon' => $query->whereBetween('due_date', [$today->copy()->addDays(2)->toDateString(), $today->copy()->addDays(7)->toDateString()]),
                default => null,
            };
        }

        if ($request->boolean('pinned_only')) {
            $query->whereHas('pins', fn ($pinQuery) => $pinQuery->where('user_id', auth()->id()));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('assigned_to', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        // Sort by due date and priority
        if ($request->filled('sort_by')) {
            if ($request->sort_by === 'newest') {
                $query->orderBy('date_added', 'desc')
                    ->orderByRaw("FIELD(priority, 'top', 'high', 'medium', 'low') ASC")
                    ->orderBy('due_date', 'asc');
            } elseif ($request->sort_by === 'oldest') {
                $query->orderBy('date_added', 'asc')
                    ->orderByRaw("FIELD(priority, 'top', 'high', 'medium', 'low') ASC")
                    ->orderBy('due_date', 'asc');
            } elseif ($request->sort_by === 'az') {
                $query->orderBy('title', 'asc')
                    ->orderByRaw("FIELD(priority, 'top', 'high', 'medium', 'low') ASC")
                    ->orderBy('due_date', 'asc');
            } elseif ($request->sort_by === 'za') {
                $query->orderBy('title', 'desc')
                    ->orderByRaw("FIELD(priority, 'top', 'high', 'medium', 'low') ASC")
                    ->orderBy('due_date', 'asc');
            } else {
                $query->orderBy('due_date', 'asc')
                    ->orderByRaw("FIELD(priority, 'top', 'high', 'medium', 'low') ASC")
                    ->orderBy('date_added', 'desc');
            }
        } else {
            $query->orderBy('due_date', 'asc')
                ->orderByRaw("FIELD(priority, 'top', 'high', 'medium', 'low') ASC")
                ->orderBy('date_added', 'desc');
        }

        if ($exportMode === 'csv') {
            $rows = $query->get()->map(function ($todo) {
                return [
                    $todo->date_added?->format('Y-m-d') ?? '—',
                    strtoupper($todo->priority),
                    $todo->assigned_to ?? 'Unassigned',
                    $todo->title,
                    $todo->description ?? '—',
                    $todo->due_date?->format('Y-m-d') ?? '—',
                    strtoupper($todo->status),
                    $todo->remarks ?? '—',
                ];
            })->all();

            return TableExport::csv('todo-report.csv', ['Date Added', 'Priority', 'Assigned To', 'Task', 'What To Do', 'Deadline', 'Status', 'Remarks'], $rows);
        }

        if (in_array($exportMode, ['print', 'pdf'], true)) {
            $availableColumns = [
                'date_added' => 'Date Added',
                'priority' => 'Priority',
                'assigned_to' => 'Assigned To',
                'task' => 'Task',
                'what_to_do' => 'What To Do',
                'deadline' => 'Deadline',
                'status' => 'Status',
                'remarks' => 'Remarks',
            ];

            $rows = $query->get()->map(function ($todo) {
                return [
                    'date_added' => $todo->date_added?->format('M d, Y') ?? '—',
                    'priority' => strtoupper($todo->priority),
                    'assigned_to' => $todo->assigned_to ?? 'Unassigned',
                    'task' => $todo->title,
                    'what_to_do' => $todo->description ?? '—',
                    'deadline' => $todo->due_date?->format('M d, Y') ?? '—',
                    'status' => strtoupper($todo->status),
                    'remarks' => $todo->remarks ?? '—',
                ];
            })->all();

            $visibleKeys = TableExport::normalizeVisibleColumns($request->get('visible_columns'), $availableColumns);
            [$headers, $printRows] = TableExport::projectRows($availableColumns, $rows, $visibleKeys);

            $responseMethod = $exportMode === 'pdf' ? 'pdfTable' : 'printTable';

            return TableExport::{$responseMethod}('Task Monitoring', $headers, $printRows, [
                'Search' => $request->search ?: 'All tasks',
            ]);
        }

        $query->with(['pins' => fn ($pinQuery) => $pinQuery->where('user_id', auth()->id())]);

        $dueReminderData = $this->buildDueReminderData();
        $assignedToOptions = $this->assignedToOptions();

        $todos = $query->paginate(15);
        $savedFilters = SavedFilter::where('user_id', auth()->id())
            ->where('module', 'todos')
            ->latest()
            ->get();

        return view('todos.index', compact('todos', 'savedFilters', 'dueReminderData', 'assignedToOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assignedToOptions = $this->assignedToOptions();

        return view('todos.create', compact('assignedToOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,top',
            'due_date' => 'nullable|date|after_or_equal:today',
            'assigned_to' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'date_added' => 'nullable|date',
            'is_recurring' => 'nullable|boolean',
            'recurrence_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1|max:30',
            'recurrence_end_date' => 'nullable|date|after_or_equal:today',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';
        $validated['is_recurring'] = $request->boolean('is_recurring');
        $validated['recurrence_interval'] = $validated['is_recurring'] ? ($validated['recurrence_interval'] ?? 1) : null;
        $validated['recurrence_frequency'] = $validated['is_recurring'] ? ($validated['recurrence_frequency'] ?? 'weekly') : null;
        $validated['recurrence_end_date'] = $validated['is_recurring'] ? ($validated['recurrence_end_date'] ?? null) : null;
        
        // If date_added is provided, use it; otherwise use current date
        if (isset($validated['date_added'])) {
            $validated['created_at'] = $validated['date_added'];
        }
        unset($validated['date_added']);

        $todo = Todo::create($validated);
        app(ActivityLogService::class)->log(
            $todo,
            'created',
            'Task created',
            auth()->user()?->name . ' created this task.',
            [
                'status' => $todo->status,
                'priority' => $todo->priority,
            ]
        );
        app(InAppNotificationService::class)->notifyTodoCreated($todo->fresh('user'), auth()->user());

        return redirect()->route('todos.index')
            ->with('success', 'Todo created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        $todo->load([
            'user',
            'pins',
            'subtasks',
            'comments.user',
            'comments.children.user',
            'activityLogs.user',
            'approval.requester',
            'approval.reviewer',
        ]);
        $exportMode = request()->get('export');

        if ($exportMode === 'csv') {
            return TableExport::csv('todo-' . $todo->id . '.csv', ['Title', 'Priority', 'Status', 'Date Added', 'Assigned To', 'Description', 'Remarks'], [[
                $todo->title,
                strtoupper($todo->priority),
                strtoupper($todo->status),
                $todo->date_added?->format('Y-m-d') ?? '—',
                $todo->assigned_to ?? 'Unassigned',
                $todo->description ?? '—',
                $todo->remarks ?? '—',
            ]]);
        }

        if (in_array($exportMode, ['print', 'pdf'], true)) {
            $responseMethod = $exportMode === 'pdf' ? 'pdfRecord' : 'printRecord';

            return TableExport::{$responseMethod}('Todo Details', [
                [
                    'title' => 'Task Information',
                    'fields' => [
                        'Title' => $todo->title,
                        'Priority' => strtoupper($todo->priority),
                        'Status' => strtoupper($todo->status),
                        'Date Added' => $todo->date_added?->format('F d, Y') ?? '—',
                        'Assigned To' => $todo->assigned_to ?? 'Unassigned',
                        'Description' => $todo->description ?? '—',
                        'Remarks' => $todo->remarks ?? '—',
                    ],
                ],
            ], [
                'Generated' => now()->format('F d, Y h:i A'),
                'Todo ID' => $todo->id,
            ]);
        }

        return view('todos.show', compact('todo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo)
    {
        $assignedToOptions = $this->assignedToOptions();

        return view('todos.edit', compact('todo', 'assignedToOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        if ($blocked = $this->ensureTodoActionAllowed($todo)) {
            return $blocked;
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,top',
            'status' => 'required|in:pending,on-going,done,cancelled',
            'due_date' => 'nullable|date|after_or_equal:today',
            'assigned_to' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'date_added' => 'nullable|date',
            'is_recurring' => 'nullable|boolean',
            'recurrence_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1|max:30',
            'recurrence_end_date' => 'nullable|date|after_or_equal:today',
        ]);

        $validated['is_recurring'] = $request->boolean('is_recurring');
        $validated['recurrence_interval'] = $validated['is_recurring'] ? ($validated['recurrence_interval'] ?? 1) : null;
        $validated['recurrence_frequency'] = $validated['is_recurring'] ? ($validated['recurrence_frequency'] ?? 'weekly') : null;
        $validated['recurrence_end_date'] = $validated['is_recurring'] ? ($validated['recurrence_end_date'] ?? null) : null;

        // If date_added is provided, update created_at
        if (isset($validated['date_added'])) {
            $validated['created_at'] = $validated['date_added'];
        }
        unset($validated['date_added']);

        $todo->update($validated);
        app(ActivityLogService::class)->log(
            $todo,
            'updated',
            'Task updated',
            auth()->user()?->name . ' updated this task.',
            [
                'status' => $todo->status,
                'priority' => $todo->priority,
            ]
        );
        app(InAppNotificationService::class)->notifyTodoUpdated($todo->fresh('user'), auth()->user());

        return redirect()->route('todos.index')
            ->with('updated', 'Todo updated successfully!');
    }

    /**
     * Update todo status via AJAX
     */
    public function updateStatus(Request $request, Todo $todo)
    {
        if ($blocked = $this->ensureTodoActionAllowed($todo, true)) {
            return $blocked;
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,on-going,done,cancelled',
        ]);

        $previousStatus = $todo->status;
        $todo->update(['status' => $validated['status']]);
        app(ActivityLogService::class)->log(
            $todo,
            'status_changed',
            'Task status changed',
            auth()->user()?->name . ' changed the task status.',
            ['status' => $todo->status]
        );
        app(InAppNotificationService::class)->notifyTodoStatusChanged($todo->fresh('user'), auth()->user());
        $this->createNextRecurringTodoIfNeeded($todo, $previousStatus, $validated['status']);

        return response()->json(['success' => true, 'status' => $validated['status']]);
    }

    /**
     * Update todo priority via AJAX
     */
    public function updatePriority(Request $request, Todo $todo)
    {
        if ($blocked = $this->ensureTodoActionAllowed($todo, true)) {
            return $blocked;
        }

        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high,top',
        ]);

        $todo->update(['priority' => $validated['priority']]);
        app(ActivityLogService::class)->log(
            $todo,
            'priority_changed',
            'Task priority changed',
            auth()->user()?->name . ' changed the task priority.',
            ['priority' => $todo->priority]
        );
        app(InAppNotificationService::class)->notifyTodoPriorityChanged($todo->fresh('user'), auth()->user());

        return response()->json(['success' => true, 'priority' => $validated['priority']]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Todo $todo)
    {
        if (!auth()->user()?->isAdmin()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Only an admin can delete tasks.'], 403);
            }

            return redirect()->route('todos.index')
                ->with('warning', 'Only an admin can delete tasks.');
        }

        if ($blocked = $this->ensureTodoActionAllowed($todo, $request->wantsJson() || $request->ajax(), 'delete')) {
            return $blocked;
        }

        try {
            $todo->delete();
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete the task.'], 500);
            }
            return redirect()->route('todos.index')
                ->with('error', 'Failed to delete the todo.');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('todos.index')
            ->with('deleted', 'Todo deleted successfully!');
    }

    /**
     * Quick update todo status or assigned_to
     */
    public function quickUpdate(Request $request, Todo $todo)
    {
        if ($blocked = $this->ensureTodoActionAllowed($todo, true)) {
            return $blocked;
        }

        $validated = $request->validate([
            'status' => 'nullable|in:pending,on-going,done,cancelled',
            'assigned_to' => 'nullable|string|max:255',
            'priority' => 'nullable|in:low,medium,high,top'
        ]);

        $previousStatus = $todo->status;
        $todo->update($validated);

        if (array_key_exists('status', $validated) && $validated['status']) {
            app(ActivityLogService::class)->log(
                $todo,
                'status_changed',
                'Task status changed',
                auth()->user()?->name . ' changed the task status.',
                ['status' => $todo->status]
            );
            app(InAppNotificationService::class)->notifyTodoStatusChanged($todo->fresh('user'), auth()->user());
            $this->createNextRecurringTodoIfNeeded($todo, $previousStatus, $todo->status);
        } elseif (array_key_exists('priority', $validated) && $validated['priority']) {
            app(ActivityLogService::class)->log(
                $todo,
                'priority_changed',
                'Task priority changed',
                auth()->user()?->name . ' changed the task priority.',
                ['priority' => $todo->priority]
            );
            app(InAppNotificationService::class)->notifyTodoPriorityChanged($todo->fresh('user'), auth()->user());
        } else {
            app(ActivityLogService::class)->log(
                $todo,
                'updated',
                'Task updated',
                auth()->user()?->name . ' updated this task.',
                [
                    'status' => $todo->status,
                    'priority' => $todo->priority,
                ]
            );
            app(InAppNotificationService::class)->notifyTodoUpdated($todo->fresh('user'), auth()->user());
        }

        return response()->json(['success' => true, 'status' => $todo->status, 'assigned_to' => $todo->assigned_to]);
    }

    private function buildDueReminderData(): array
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $nextWeek = Carbon::today()->addDays(7);
        $user = auth()->user();
        $normalizedName = mb_strtolower(trim((string) $user?->name));

        $query = Todo::query()
            ->whereNotIn('status', ['done', 'cancelled'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', $nextWeek);

        if (!$user?->isAdmin()) {
            $query->where(function ($builder) use ($user, $normalizedName) {
                $builder->where('user_id', $user?->id);

                if ($normalizedName !== '') {
                    $builder->orWhereRaw('LOWER(assigned_to) LIKE ?', ['%' . $normalizedName . '%']);
                }
            });
        }

        $items = $query->orderBy('due_date')->limit(8)->get()->map(function (Todo $todo) use ($today, $tomorrow) {
            $dueDate = $todo->due_date;
            $daysUntil = $dueDate ? $today->diffInDays($dueDate, false) : null;

            if ($dueDate && $dueDate->lt($today)) {
                $level = 'overdue';
                $label = 'Overdue';
            } elseif ($dueDate && $dueDate->isSameDay($today)) {
                $level = 'today';
                $label = 'Due Today';
            } elseif ($dueDate && $dueDate->isSameDay($tomorrow)) {
                $level = 'tomorrow';
                $label = 'Due Tomorrow';
            } else {
                $level = 'soon';
                $label = $daysUntil !== null ? 'Due in ' . $daysUntil . ' day' . ($daysUntil === 1 ? '' : 's') : 'Upcoming';
            }

            return [
                'todo' => $todo,
                'level' => $level,
                'label' => $label,
                'days_until' => $daysUntil,
            ];
        });

        return [
            'items' => $items,
            'counts' => [
                'overdue' => $items->where('level', 'overdue')->count(),
                'today' => $items->where('level', 'today')->count(),
                'tomorrow' => $items->where('level', 'tomorrow')->count(),
                'soon' => $items->where('level', 'soon')->count(),
            ],
        ];
    }

    private function assignedToOptions(): array
    {
        return Todo::query()
            ->whereNotNull('assigned_to')
            ->where('assigned_to', '!=', '')
            ->distinct()
            ->orderBy('assigned_to')
            ->pluck('assigned_to')
            ->values()
            ->all();
    }

    private function createNextRecurringTodoIfNeeded(Todo $todo, ?string $previousStatus, ?string $newStatus): void
    {
        if (!$todo->is_recurring || $previousStatus === 'done' || $newStatus !== 'done') {
            return;
        }

        $anchorDate = $todo->due_date ?? $todo->date_added ?? $todo->created_at?->toDate();
        if (!$anchorDate) {
            return;
        }

        $nextDate = Carbon::parse($anchorDate);
        $interval = max((int) ($todo->recurrence_interval ?? 1), 1);

        match ($todo->recurrence_frequency) {
            'daily' => $nextDate->addDays($interval),
            'weekly' => $nextDate->addWeeks($interval),
            'monthly' => $nextDate->addMonths($interval),
            'yearly' => $nextDate->addYears($interval),
            default => $nextDate->addWeek(),
        };

        if ($todo->recurrence_end_date && $nextDate->gt(Carbon::parse($todo->recurrence_end_date))) {
            return;
        }

        $parentId = $todo->recurring_parent_id ?: $todo->id;

        $alreadyExists = Todo::query()
            ->where('recurring_parent_id', $parentId)
            ->whereDate('due_date', $nextDate->toDateString())
            ->exists();

        if ($alreadyExists) {
            return;
        }

        $nextTodo = Todo::create([
            'title' => $todo->title,
            'description' => $todo->description,
            'priority' => $todo->priority,
            'status' => 'pending',
            'due_date' => $nextDate->toDateString(),
            'user_id' => $todo->user_id,
            'assigned_to' => $todo->assigned_to,
            'remarks' => $todo->remarks,
            'date_added' => now()->toDateString(),
            'is_recurring' => true,
            'recurrence_frequency' => $todo->recurrence_frequency,
            'recurrence_interval' => $todo->recurrence_interval,
            'recurrence_end_date' => $todo->recurrence_end_date,
            'recurring_parent_id' => $parentId,
        ]);

        app(ActivityLogService::class)->log(
            $nextTodo,
            'created',
            'Recurring task created',
            'A new recurring task instance was created automatically.',
            [
                'source_todo_id' => $todo->id,
                'due_date' => $nextTodo->due_date?->format('Y-m-d'),
            ]
        );
    }
}
