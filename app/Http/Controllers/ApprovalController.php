<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Document;
use App\Models\FinancialRecord;
use App\Models\Todo;
use App\Services\ActivityLogService;
use App\Services\InAppNotificationService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function requestApproval(Request $request)
    {
        $validated = $request->validate([
            'subject_type' => 'required|in:todo,document,financial',
            'subject_id' => 'required|integer',
            'request_note' => 'nullable|string|max:3000',
        ]);

        $subject = $this->resolveSubject($validated['subject_type'], (int) $validated['subject_id']);
        $approval = $subject->approval()->firstOrNew();

        if ($approval->exists && $approval->status === Approval::STATUS_PENDING) {
            return back()->with('warning', 'This record already has a pending approval request.');
        }

        $approval->fill([
            'requested_by' => auth()->id(),
            'reviewed_by' => null,
            'status' => Approval::STATUS_PENDING,
            'request_note' => $validated['request_note'] ?? null,
            'review_note' => null,
            'requested_at' => now(),
            'reviewed_at' => null,
        ])->save();

        app(ActivityLogService::class)->log(
            $subject,
            'approval_requested',
            'Approval requested',
            auth()->user()?->name . ' requested approval.',
            ['note' => $validated['request_note'] ?? null]
        );

        app(InAppNotificationService::class)->notifyAdmins([
            'title' => 'Approval Request',
            'message' => auth()->user()?->name . ' requested approval for ' . $this->subjectLabel($subject) . '.',
            'url' => $this->subjectUrl($subject),
            'type' => 'warning',
            'icon' => 'fa-solid fa-user-check',
            'category' => 'approval',
        ]);

        return back()->with('success', 'Approval requested successfully.');
    }

    public function review(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'subject_type' => 'required|in:todo,document,financial',
            'subject_id' => 'required|integer',
            'decision' => 'required|in:approved,rejected',
            'review_note' => 'nullable|string|max:3000',
        ]);

        $subject = $this->resolveSubject($validated['subject_type'], (int) $validated['subject_id']);
        $approval = $subject->approval()->firstOrNew();

        if (!$approval->exists || $approval->status !== Approval::STATUS_PENDING) {
            return back()->with('warning', 'There is no pending approval request to review.');
        }

        $approval->fill([
            'reviewed_by' => auth()->id(),
            'status' => $validated['decision'],
            'review_note' => $validated['review_note'] ?? null,
            'reviewed_at' => now(),
        ])->save();

        app(ActivityLogService::class)->log(
            $subject,
            'approval_' . $validated['decision'],
            'Approval ' . ucfirst($validated['decision']),
            auth()->user()?->name . ' ' . $validated['decision'] . ' this record.',
            ['note' => $validated['review_note'] ?? null]
        );

        $requester = $approval->requester;
        if ($requester) {
            app(InAppNotificationService::class)->notifyUsers([$requester], [
                'title' => 'Approval ' . ucfirst($validated['decision']),
                'message' => auth()->user()?->name . ' ' . $validated['decision'] . ' your request for ' . $this->subjectLabel($subject) . '.',
                'url' => $this->subjectUrl($subject),
                'type' => $validated['decision'] === 'approved' ? 'success' : 'danger',
                'icon' => $validated['decision'] === 'approved' ? 'fa-solid fa-badge-check' : 'fa-solid fa-circle-xmark',
                'category' => 'approval',
            ]);
        }

        app(InAppNotificationService::class)->notifyAdmins([
            'title' => 'Approval ' . ucfirst($validated['decision']),
            'message' => auth()->user()?->name . ' ' . $validated['decision'] . ' ' . $this->subjectLabel($subject) . '.',
            'url' => $this->subjectUrl($subject),
            'type' => $validated['decision'] === 'approved' ? 'success' : 'danger',
            'icon' => $validated['decision'] === 'approved' ? 'fa-solid fa-badge-check' : 'fa-solid fa-circle-xmark',
            'category' => 'approval',
        ]);

        return back()->with('success', 'Approval review saved.');
    }

    private function resolveSubject(string $type, int $id)
    {
        return match ($type) {
            'todo' => Todo::findOrFail($id),
            'document' => Document::findOrFail($id),
            'financial' => FinancialRecord::findOrFail($id),
        };
    }

    private function subjectLabel($subject): string
    {
        return match (true) {
            $subject instanceof Todo => 'task "' . $subject->title . '"',
            $subject instanceof Document => 'document "' . ($subject->subject ?: $subject->dts_number) . '"',
            $subject instanceof FinancialRecord => 'financial record "' . ($subject->description ?: $subject->type) . '"',
            default => 'record',
        };
    }

    private function subjectUrl($subject): string
    {
        return match (true) {
            $subject instanceof Todo => route('todos.show', $subject),
            $subject instanceof Document => route('documents.show', $subject),
            $subject instanceof FinancialRecord => route('financial.show', $subject),
            default => route('dashboard'),
        };
    }
}
