<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SystemNotification;

class EmailNotificationService
{
    /**
     * Send a document status notification
     */
    public function sendDocumentNotification(User $user, string $documentTitle, string $status, string $actionUrl = null): void
    {
        $subject = "Document Status Update: {$documentTitle}";
        $title = "Document Status Changed";
        $message = "Your document '{$documentTitle}' status has been updated to: {$status}";
        
        $details = [
            "Document: {$documentTitle}",
            "New Status: {$status}",
            "Updated by: " . auth()->user()->name ?? 'System',
        ];

        $user->notify(new SystemNotification(
            $subject,
            $title,
            $message,
            $actionUrl,
            'View Document',
            $details,
            'DOC-' . time()
        ));
    }

    /**
     * Send a financial record notification
     */
    public function sendFinancialNotification(User $user, string $recordType, string $amount, string $status, string $actionUrl = null): void
    {
        $subject = "Financial Record Update: {$recordType}";
        $title = "Financial Record Status Changed";
        $message = "Your financial record '{$recordType}' with amount {$amount} has been updated to: {$status}";
        
        $details = [
            "Record Type: {$recordType}",
            "Amount: {$amount}",
            "Status: {$status}",
            "Updated by: " . auth()->user()->name ?? 'System',
        ];

        $user->notify(new SystemNotification(
            $subject,
            $title,
            $message,
            $actionUrl,
            'View Record',
            $details,
            'FIN-' . time()
        ));
    }

    /**
     * Send a task assignment notification
     */
    public function sendTaskAssignmentNotification(User $user, string $taskTitle, string $description, string $dueDate, string $actionUrl = null): void
    {
        $subject = "New Task Assigned: {$taskTitle}";
        $title = "Task Assignment";
        $message = "You have been assigned a new task: {$taskTitle}";
        
        $details = [
            "Task: {$taskTitle}",
            "Description: {$description}",
            "Due Date: {$dueDate}",
            "Assigned by: " . auth()->user()->name ?? 'System',
        ];

        $user->notify(new SystemNotification(
            $subject,
            $title,
            $message,
            $actionUrl,
            'View Task',
            $details,
            'TASK-' . time()
        ));
    }

    /**
     * Send a system alert notification
     */
    public function sendSystemAlert(User $user, string $alertType, string $message, string $actionUrl = null): void
    {
        $subject = "System Alert: {$alertType}";
        $title = "System Alert";
        
        $details = [
            "Alert Type: {$alertType}",
            "Time: " . now()->format('Y-m-d H:i:s'),
            "User: " . $user->name,
        ];

        $user->notify(new SystemNotification(
            $subject,
            $title,
            $message,
            $actionUrl,
            'View Details',
            $details,
            'ALERT-' . time()
        ));
    }

    /**
     * Send a welcome email to new users
     */
    public function sendWelcomeEmail(User $user): void
    {
        $subject = "Welcome to PICTO - RTS";
        $title = "Welcome to PICTO - Records and Tracking System";
        $message = "Welcome {$user->name}! Your account has been created successfully. You can now start using the PICTO - RTS system.";
        
        $details = [
            "Account Email: {$user->email}",
            "Office: " . $user->office->name ?? 'Not Assigned',
            "Role: " . ucfirst($user->role),
            "Joined: " . $user->created_at->format('Y-m-d H:i:s'),
        ];

        $user->notify(new SystemNotification(
            $subject,
            $title,
            $message,
            route('dashboard'),
            'Go to Dashboard',
            $details,
            'WELCOME-' . $user->id
        ));
    }

    /**
     * Send notification to multiple users (admin users)
     */
    public function sendToAdmins(string $subject, string $title, string $message, ?string $actionUrl = null): void
    {
        $adminUsers = User::where('role', User::ROLE_ADMIN)->get();
        
        foreach ($adminUsers as $admin) {
            if ($admin instanceof User) {
                $admin->notify(new SystemNotification(
                    $subject,
                    $title,
                    $message,
                    $actionUrl,
                    'View Details',
                    ["Sent to all administrators"],
                    'ADMIN-' . time()
                ));
            }
        }
    }

    /**
     * Send notification to all users in an office
     */
    public function sendToOffice(int $officeId, string $subject, string $title, string $message, ?string $actionUrl = null): void
    {
        $officeUsers = User::where('office_id', $officeId)->get();
        
        foreach ($officeUsers as $user) {
            if ($user instanceof User) {
                $user->notify(new SystemNotification(
                    $subject,
                    $title,
                    $message,
                    $actionUrl,
                    'View Details',
                    ["Sent to all users in your office"],
                    'OFFICE-' . $officeId . '-' . time()
                ));
            }
        }
    }
}
