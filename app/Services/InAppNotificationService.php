<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Document;
use App\Models\FinancialRecord;
use App\Models\Todo;
use App\Models\User;
use App\Notifications\InAppActivityNotification;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class InAppNotificationService
{
    public function notifyUsers(iterable $users, array $payload, ?int $excludeUserId = null): void
    {
        $normalizedUsers = $this->normalizeUsers($users)
            ->when($excludeUserId, fn (Collection $items) => $items->where('id', '!=', $excludeUserId))
            ->unique('id');

        $category = $payload['category'] ?? 'general';

        $normalizedUsers
            ->filter(fn (User $user) => $user->wantsNotificationCategory($category))
            ->each(function (User $user) use ($payload) {
                $user->notify(new InAppActivityNotification($payload));
            });
    }

    public function notifyOfficeUsers(int $officeId, array $payload, ?int $excludeUserId = null): void
    {
        $users = User::query()
            ->where('office_id', $officeId)
            ->get();

        $this->notifyUsers($users, $payload, $excludeUserId);
    }

    public function notifyAdmins(array $payload, ?int $excludeUserId = null): void
    {
        $users = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->get();

        $this->notifyUsers($users, $payload, $excludeUserId);
    }

    public function notifyTodoCreated(Todo $todo, ?User $actor = null): void
    {
        $payload = [
            'title' => 'New To-Do Task',
            'message' => sprintf(
                '%s created the task "%s"%s.',
                $actor?->name ?? 'A user',
                $todo->title,
                $todo->assigned_to ? ' and assigned it to ' . $todo->assigned_to : ''
            ),
            'url' => route('todos.show', $todo),
            'type' => 'info',
            'icon' => 'fa-solid fa-list-check',
            'category' => 'todo',
            'actor_name' => $actor?->name,
            'meta' => [
                'todo_id' => $todo->id,
                'status' => $todo->status,
                'priority' => $todo->priority,
            ],
        ];

        $recipients = $this->todoWatchers($todo)
            ->when($actor, fn (Collection $items) => $items->push($actor));
        $this->notifyUsers($recipients, $payload);
        $this->notifyAdmins($payload);
    }

    public function notifyTodoUpdated(Todo $todo, ?User $actor = null): void
    {
        $payload = [
            'title' => 'To-Do Updated',
            'message' => sprintf(
                '%s updated "%s". Status: %s. Priority: %s.',
                $actor?->name ?? 'A user',
                $todo->title,
                strtoupper($todo->status),
                strtoupper($todo->priority)
            ),
            'url' => route('todos.show', $todo),
            'type' => 'info',
            'icon' => 'fa-solid fa-pen-to-square',
            'category' => 'todo',
            'actor_name' => $actor?->name,
            'meta' => [
                'todo_id' => $todo->id,
                'status' => $todo->status,
                'priority' => $todo->priority,
            ],
        ];

        $watchers = $this->todoWatchers($todo)
            ->when($actor, fn (Collection $items) => $items->push($actor));
        $this->notifyUsers($watchers, $payload);
        $this->notifyAdmins($payload);
    }

    public function notifyTodoStatusChanged(Todo $todo, ?User $actor = null): void
    {
        $payload = [
            'title' => 'Task Status Changed',
            'message' => sprintf(
                '%s changed "%s" to %s.',
                $actor?->name ?? 'A user',
                $todo->title,
                strtoupper($todo->status)
            ),
            'url' => route('todos.show', $todo),
            'type' => $todo->status === 'done' ? 'success' : 'info',
            'icon' => 'fa-solid fa-circle-check',
            'category' => 'todo',
            'actor_name' => $actor?->name,
            'meta' => [
                'todo_id' => $todo->id,
                'status' => $todo->status,
            ],
        ];

        $watchers = $this->todoWatchers($todo)
            ->when($actor, fn (Collection $items) => $items->push($actor));
        $this->notifyUsers($watchers, $payload);
        $this->notifyAdmins($payload);
    }

    public function notifyTodoPriorityChanged(Todo $todo, ?User $actor = null): void
    {
        $payload = [
            'title' => 'Task Priority Changed',
            'message' => sprintf(
                '%s updated the priority of "%s" to %s.',
                $actor?->name ?? 'A user',
                $todo->title,
                strtoupper($todo->priority)
            ),
            'url' => route('todos.show', $todo),
            'type' => 'warning',
            'icon' => 'fa-solid fa-flag',
            'category' => 'todo',
            'actor_name' => $actor?->name,
            'meta' => [
                'todo_id' => $todo->id,
                'priority' => $todo->priority,
            ],
        ];

        $watchers = $this->todoWatchers($todo)
            ->when($actor, fn (Collection $items) => $items->push($actor));
        $this->notifyUsers($watchers, $payload);
        $this->notifyAdmins($payload);
    }

    public function notifyDocumentForwarded(Document $document, int $toOfficeId, ?User $actor = null): void
    {
        $toOfficeName = $document->currentOffice?->name
            ?? optional($document->destinationOffice)->name
            ?? 'your office';

        $payload = [
            'title' => 'Document Forwarded',
            'message' => sprintf(
                '%s forwarded document "%s" to %s.',
                $actor?->name ?? 'A user',
                $document->subject ?: ($document->dts_number ?? 'Untitled document'),
                $toOfficeName
            ),
            'url' => route('documents.show', $document),
            'type' => 'info',
            'icon' => 'fa-solid fa-share-from-square',
            'category' => 'document',
            'actor_name' => $actor?->name,
            'meta' => [
                'document_id' => $document->id,
                'dts_number' => $document->dts_number,
            ],
        ];

        $watchers = User::query()
            ->where('office_id', $toOfficeId)
            ->get()
            ->when($actor, fn (Collection $items) => $items->push($actor));

        $this->notifyUsers($watchers, $payload);
        $this->notifyAdmins($payload);
    }

    public function notifyDocumentReceived(Document $document, ?User $actor = null): void
    {
        $watchers = collect([$document->encoder, $document->holder])->filter();

        $payload = [
            'title' => 'Document Received',
            'message' => sprintf(
                '%s marked document "%s" as received.',
                $actor?->name ?? 'A user',
                $document->subject ?: ($document->dts_number ?? 'Untitled document')
            ),
            'url' => route('documents.show', $document),
            'type' => 'success',
            'icon' => 'fa-solid fa-inbox',
            'category' => 'document',
            'actor_name' => $actor?->name,
            'meta' => [
                'document_id' => $document->id,
                'dts_number' => $document->dts_number,
            ],
        ];

        if ($actor) {
            $watchers = $watchers->push($actor);
        }

        $this->notifyUsers($watchers, $payload);
        $this->notifyAdmins($payload);
    }

    public function notifyFinancialForwarded(FinancialRecord $financial, int $toOfficeId, ?User $actor = null): void
    {
        $payload = [
            'title' => 'Financial Record Forwarded',
            'message' => sprintf(
                '%s forwarded "%s" to your office.',
                $actor?->name ?? 'A user',
                $financial->description ?: $financial->type
            ),
            'url' => route('financial.show', $financial),
            'type' => 'info',
            'icon' => 'fa-solid fa-money-bill-transfer',
            'category' => 'financial',
            'actor_name' => $actor?->name,
            'meta' => [
                'financial_id' => $financial->id,
                'type' => $financial->type,
            ],
        ];

        $watchers = User::query()
            ->where('office_id', $toOfficeId)
            ->get()
            ->when($actor, fn (Collection $items) => $items->push($actor));

        $this->notifyUsers($watchers, $payload);
        $this->notifyAdmins($payload);
    }

    public function notifyFinancialStatusChanged(FinancialRecord $financial, ?User $actor = null): void
    {
        $watchers = collect([$financial->createdBy, $financial->holder])
            ->merge($this->officeUsers($financial->current_office))
            ->merge($this->officeUsers($financial->office_origin))
            ->filter();

        $payload = [
            'title' => 'Financial Status Updated',
            'message' => sprintf(
                '%s changed "%s" to %s.',
                $actor?->name ?? 'A user',
                $financial->description ?: $financial->type,
                strtoupper($financial->status)
            ),
            'url' => route('financial.show', $financial),
            'type' => $financial->status === 'FINISHED' ? 'success' : 'info',
            'icon' => 'fa-solid fa-file-invoice-dollar',
            'category' => 'financial',
            'actor_name' => $actor?->name,
            'meta' => [
                'financial_id' => $financial->id,
                'status' => $financial->status,
            ],
        ];

        if ($actor) {
            $watchers = $watchers->push($actor);
        }

        $this->notifyUsers($watchers, $payload);
        $this->notifyAdmins($payload);
    }

    public function notifyFinancialReceived(FinancialRecord $financial, ?User $actor = null): void
    {
        $watchers = collect([$financial->createdBy])->filter();

        $payload = [
            'title' => 'Financial Record Received',
            'message' => sprintf(
                '%s received "%s".',
                $actor?->name ?? 'A user',
                $financial->description ?: $financial->type
            ),
            'url' => route('financial.show', $financial),
            'type' => 'success',
            'icon' => 'fa-solid fa-circle-down',
            'category' => 'financial',
            'actor_name' => $actor?->name,
            'meta' => [
                'financial_id' => $financial->id,
            ],
        ];

        if ($actor) {
            $watchers = $watchers->push($actor);
        }

        $this->notifyUsers($watchers, $payload);
        $this->notifyAdmins($payload);
    }

    public function notifyCommentAdded($subject, Comment $comment, ?User $actor = null, bool $isReply = false): void
    {
        $label = $this->subjectLabel($subject);
        $payload = [
            'title' => $isReply ? 'Comment Reply Added' : 'New Comment Added',
            'message' => sprintf(
                '%s %s on %s.',
                $actor?->name ?? 'A user',
                $isReply ? 'replied to a comment' : 'added a comment',
                $label
            ),
            'url' => $this->subjectUrl($subject),
            'type' => 'info',
            'icon' => 'fa-solid fa-comments',
            'category' => $this->subjectCategory($subject),
            'actor_name' => $actor?->name,
            'meta' => [
                'comment_id' => $comment->id,
                'subject_type' => class_basename($comment->commentable_type),
                'subject_id' => $comment->commentable_id,
            ],
        ];

        $recipients = $this->subjectUsers($subject)
            ->when($actor, fn (Collection $items) => $items->push($actor));

        $this->notifyUsers($recipients, $payload);
        $this->notifyAdmins($payload);
    }

    public function notifyCommentDeleted($subject, ?User $actor = null): void
    {
        $payload = [
            'title' => 'Comment Deleted',
            'message' => sprintf(
                '%s deleted a comment on %s.',
                $actor?->name ?? 'A user',
                $this->subjectLabel($subject)
            ),
            'url' => $this->subjectUrl($subject),
            'type' => 'warning',
            'icon' => 'fa-solid fa-comment-slash',
            'category' => $this->subjectCategory($subject),
            'actor_name' => $actor?->name,
        ];

        $recipients = $this->subjectUsers($subject)
            ->when($actor, fn (Collection $items) => $items->push($actor));

        $this->notifyUsers($recipients, $payload);
        $this->notifyAdmins($payload);
    }

    private function normalizeUsers(iterable $users): Collection
    {
        if ($users instanceof EloquentCollection) {
            return $users->filter();
        }

        return collect($users)->flatten()->filter(fn ($user) => $user instanceof User);
    }

    private function todoWatchers(Todo $todo): Collection
    {
        $assignedUsers = $this->findUsersByAssignedTo($todo->assigned_to);

        return collect([$todo->user])
            ->merge($assignedUsers)
            ->filter();
    }

    private function findUsersByAssignedTo(?string $assignedTo): Collection
    {
        $name = trim((string) $assignedTo);

        if ($name === '') {
            return collect();
        }

        return User::query()
            ->where('name', 'like', '%' . $name . '%')
            ->orWhereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->get();
    }

    private function officeUsers(?int $officeId): Collection
    {
        if (!$officeId) {
            return collect();
        }

        return User::query()
            ->where('office_id', $officeId)
            ->get();
    }

    private function adminUsers(): Collection
    {
        return User::query()
            ->where('role', User::ROLE_ADMIN)
            ->get();
    }

    private function subjectUsers($subject): Collection
    {
        return match (true) {
            $subject instanceof Todo => $this->todoWatchers($subject),
            $subject instanceof Document => collect([$subject->encoder, $subject->holder])
                ->merge($this->officeUsers($subject->current_office))
                ->merge($this->officeUsers($subject->destination_office))
                ->filter(),
            $subject instanceof FinancialRecord => collect([$subject->createdBy, $subject->holder])
                ->merge($this->officeUsers($subject->current_office))
                ->merge($this->officeUsers($subject->office_origin))
                ->filter(),
            default => collect(),
        };
    }

    private function subjectLabel($subject): string
    {
        return match (true) {
            $subject instanceof Todo => 'task "' . $subject->title . '"',
            $subject instanceof Document => 'document "' . ($subject->subject ?: $subject->dts_number ?: 'Untitled document') . '"',
            $subject instanceof FinancialRecord => 'financial record "' . ($subject->description ?: $subject->type ?: 'Untitled record') . '"',
            default => 'this record',
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

    private function subjectCategory($subject): string
    {
        return match (true) {
            $subject instanceof Todo => 'todo',
            $subject instanceof Document => 'document',
            $subject instanceof FinancialRecord => 'financial',
            default => 'general',
        };
    }
}
