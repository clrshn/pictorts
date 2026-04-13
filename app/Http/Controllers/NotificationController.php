<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EmailNotificationService;
use Illuminate\Http\Request;
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
        $users = User::all();
        return view('notifications.index', compact('users'));
    }
}
