<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Support\TableExport;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Todo::query();

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

        if ($request->get('export') === 'csv') {
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

        if ($request->get('export') === 'print') {
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

            return TableExport::printTable('Task Monitoring', $headers, $printRows, [
                'Search' => $request->search ?: 'All tasks',
            ]);
        }

        $todos = $query->paginate(15);

        return view('todos.index', compact('todos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('todos.create');
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
        ]);

        $validated['user_id'] = auth()->id();
        
        // If date_added is provided, use it; otherwise use current date
        if (isset($validated['date_added'])) {
            $validated['created_at'] = $validated['date_added'];
        }
        unset($validated['date_added']);

        Todo::create($validated);

        return redirect()->route('todos.index')
            ->with('success', 'Todo created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        if (request()->get('export') === 'csv') {
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

        if (request()->get('export') === 'print') {
            return TableExport::printRecord('Todo Details', [
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
        return view('todos.edit', compact('todo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,top',
            'status' => 'required|in:pending,on-going,done,cancelled',
            'due_date' => 'nullable|date|after_or_equal:today',
            'assigned_to' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'date_added' => 'nullable|date',
        ]);

        // If date_added is provided, update created_at
        if (isset($validated['date_added'])) {
            $validated['created_at'] = $validated['date_added'];
        }
        unset($validated['date_added']);

        $todo->update($validated);

        return redirect()->route('todos.index')
            ->with('updated', 'Todo updated successfully!');
    }

    /**
     * Update todo status via AJAX
     */
    public function updateStatus(Request $request, Todo $todo)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,on-going,done,cancelled',
        ]);

        $todo->update(['status' => $validated['status']]);

        return response()->json(['success' => true, 'status' => $validated['status']]);
    }

    /**
     * Update todo priority via AJAX
     */
    public function updatePriority(Request $request, Todo $todo)
    {
        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high,top',
        ]);

        $todo->update(['priority' => $validated['priority']]);

        return response()->json(['success' => true, 'priority' => $validated['priority']]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Todo $todo)
    {
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
        $validated = $request->validate([
            'status' => 'nullable|in:pending,on-going,done,cancelled',
            'assigned_to' => 'nullable|string|max:255',
            'priority' => 'nullable|in:low,medium,high,top'
        ]);

        $todo->update($validated);

        return response()->json(['success' => true, 'status' => $todo->status, 'assigned_to' => $todo->assigned_to]);
    }
}
