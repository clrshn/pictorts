<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Document;
use App\Models\FinancialRecord;
use App\Models\Todo;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_type' => 'required|in:todo,document,financial',
            'subject_id' => 'required|integer',
            'body' => 'required|string|max:3000',
            'parent_id' => 'nullable|integer|exists:comments,id',
        ]);

        $subject = $this->resolveSubject($validated['subject_type'], (int) $validated['subject_id']);

        $parent = null;
        if (!empty($validated['parent_id'])) {
            $parent = Comment::query()->findOrFail((int) $validated['parent_id']);

            abort_unless(
                $parent->commentable_type === $subject::class && (int) $parent->commentable_id === (int) $subject->getKey(),
                422,
                'The selected parent comment does not belong to this record.'
            );
        }

        $comment = $subject->comments()->create([
            'user_id' => auth()->id(),
            'parent_id' => $parent?->id,
            'body' => $validated['body'],
        ]);

        app(ActivityLogService::class)->log(
            $subject,
            $parent ? 'comment_replied' : 'comment_added',
            $parent ? 'Comment replied to' : 'Comment added',
            auth()->user()?->name . ($parent ? ' replied to a comment.' : ' added a comment.'),
            ['body' => $validated['body']]
        );

        return back()->with('success', $parent ? 'Reply posted successfully.' : 'Comment added successfully.');
    }

    public function destroy(Comment $comment)
    {
        abort_unless(
            auth()->id() === (int) $comment->user_id || auth()->user()?->isAdmin(),
            403
        );

        $subject = $comment->commentable;
        $comment->delete();

        if ($subject) {
            app(ActivityLogService::class)->log(
                $subject,
                'comment_deleted',
                'Comment deleted',
                auth()->user()?->name . ' deleted a comment.'
            );

        }

        return back()->with('success', 'Comment deleted successfully.');
    }

    private function resolveSubject(string $type, int $id)
    {
        return match ($type) {
            'todo' => Todo::findOrFail($id),
            'document' => Document::findOrFail($id),
            'financial' => FinancialRecord::findOrFail($id),
        };
    }
}
