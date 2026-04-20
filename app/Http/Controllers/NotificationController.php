<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use App\Services\EmailNotificationService;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Send test notification to current user
     */
    public function sendTestNotification(Request $request)
    {
        $user = Auth::user();
        
        $this->emailService->sendSystemAlert(
            $user,
            'Test Alert',
            'This is a test notification from the PICTO - RTS system. If you receive this email, the email functionality is working correctly!',
            route('dashboard')
        );

        return response()->json(['message' => 'Test notification sent successfully!']);
    }

    /**
     * Send document notification
     */
    public function sendDocumentNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'document_title' => 'required|string',
            'status' => 'required|string',
        ]);

        $user = User::find($request->user_id);
        
        $this->emailService->sendDocumentNotification(
            $user,
            $request->document_title,
            $request->status,
            route('documents.index')
        );

        return response()->json(['message' => 'Document notification sent successfully!']);
    }

    /**
     * Send financial notification
     */
    public function sendFinancialNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'record_type' => 'required|string',
            'amount' => 'required|string',
            'status' => 'required|string',
        ]);

        $user = User::find($request->user_id);
        
        $this->emailService->sendFinancialNotification(
            $user,
            $request->record_type,
            $request->amount,
            $request->status,
            route('financial.index')
        );

        return response()->json(['message' => 'Financial notification sent successfully!']);
    }

    /**
     * Send task assignment notification
     */
    public function sendTaskNotification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'task_title' => 'required|string',
            'description' => 'required|string',
            'due_date' => 'required|string',
        ]);

        $user = User::find($request->user_id);
        
        $this->emailService->sendTaskAssignmentNotification(
            $user,
            $request->task_title,
            $request->description,
            $request->due_date,
            route('todos.index')
        );

        return response()->json(['message' => 'Task notification sent successfully!']);
    }

    /**
     * Send welcome email to user
     */
    public function sendWelcomeEmail(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);
        
        $this->emailService->sendWelcomeEmail($user);

        return response()->json(['message' => 'Welcome email sent successfully!']);
    }

    /**
     * Send notification to all admins
     */
    public function sendToAdmins(Request $request)
    {
        $request->validate([
            'subject' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        $this->emailService->sendToAdmins(
            $request->subject,
            $request->title,
            $request->message,
            route('dashboard')
        );

        return response()->json(['message' => 'Notification sent to all admins successfully!']);
    }

    /**
     * Send notification to office users
     */
    public function sendToOffice(Request $request)
    {
        $request->validate([
            'office_id' => 'required|exists:offices,id',
            'subject' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        $this->emailService->sendToOffice(
            $request->office_id,
            $request->subject,
            $request->title,
            $request->message,
            route('dashboard')
        );

        return response()->json(['message' => 'Notification sent to office users successfully!']);
    }

    /**
     * Show notification testing page
     */
    public function index()
    {
        return view('notifications.index');
    }

    /**
     * Return the authenticated user's notification feed.
     */
    public function feed()
    {
        $user = Auth::user();
        $limit = max(10, min((int) request('limit', 15), 100));
        $category = request('category');
        $onlyUnread = request()->boolean('unread');

        $databaseNotifications = $user->notifications()
            ->when($onlyUnread, fn ($query) => $query->whereNull('read_at'))
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (DatabaseNotification $notification) => [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'Notification',
                'message' => $notification->data['message'] ?? '',
                'url' => $notification->data['url'] ?? null,
                'type' => $notification->data['type'] ?? 'info',
                'icon' => $notification->data['icon'] ?? 'fa-solid fa-bell',
                'category' => $notification->data['category'] ?? 'general',
                'read_at' => $notification->read_at?->toIso8601String(),
                'created_at' => $notification->created_at?->toIso8601String(),
                'time_label' => $notification->created_at?->diffForHumans(),
                'synthetic' => false,
            ]);

        $reminders = $this->buildTodoReminders($user);
        $items = $databaseNotifications
            ->concat($reminders)
            ->when($category && $category !== 'all', function ($collection) use ($category) {
                return $collection->filter(fn (array $item) => ($item['category'] ?? 'general') === $category);
            })
            ->sortByDesc(fn (array $item) => $item['created_at'] ?? now()->toIso8601String())
            ->values()
            ->take($limit)
            ->all();

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count() + $reminders->count(),
            'items' => $items,
        ]);
    }

    /**
     * Mark a database notification as read.
     */
    public function markRead(string $notificationId)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all database notifications as read.
     */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    private function buildTodoReminders(User $user)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $normalizedName = mb_strtolower(trim($user->name));

        $todos = Todo::query()
            ->whereNotIn('status', ['done', 'cancelled'])
            ->where(function ($query) use ($user, $normalizedName) {
                $query->where('user_id', $user->id);

                if ($normalizedName !== '') {
                    $query->orWhereRaw('LOWER(assigned_to) LIKE ?', ['%' . $normalizedName . '%']);
                }
            })
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', $tomorrow)
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return $todos->map(function (Todo $todo) use ($today, $tomorrow) {
            $dueDate = $todo->due_date;
            $isOverdue = $dueDate && $dueDate->lt($today);
            $isToday = $dueDate && $dueDate->isSameDay($today);
            $isTomorrow = $dueDate && $dueDate->isSameDay($tomorrow);

            if ($isOverdue) {
                $title = 'Overdue Task Reminder';
                $message = sprintf('"%s" is overdue since %s.', $todo->title, $dueDate->format('M d, Y'));
                $type = 'danger';
                $icon = 'fa-solid fa-clock';
            } elseif ($isToday) {
                $title = 'Task Due Today';
                $message = sprintf('"%s" is due today.', $todo->title);
                $type = 'warning';
                $icon = 'fa-solid fa-hourglass-half';
            } else {
                $title = 'Task Due Tomorrow';
                $message = sprintf('"%s" is due tomorrow.', $todo->title);
                $type = 'info';
                $icon = 'fa-solid fa-calendar-day';
            }

            return [
                'id' => 'todo-reminder-' . $todo->id,
                'title' => $title,
                'message' => $message,
                'url' => route('todos.show', $todo),
                'type' => $type,
                'icon' => $icon,
                'category' => 'reminder',
                'read_at' => null,
                'created_at' => $todo->updated_at?->toIso8601String() ?? now()->toIso8601String(),
                'time_label' => $dueDate ? 'Due ' . $dueDate->format('M d, Y') : 'Reminder',
                'synthetic' => true,
            ];
        });
    }
}
