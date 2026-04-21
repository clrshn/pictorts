<?php

namespace App\Http\Controllers;

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
        ]);

        $subject = $this->resolveSubject($validated['subject_type'], (int) $validated['subject_id']);

        $subject->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        app(ActivityLogService::class)->log(
            $subject,
            'comment_added',
            'Comment added',
            auth()->user()?->name . ' added a comment.',
            ['body' => $validated['body']]
        );

        return back()->with('success', 'Comment added successfully.');
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
