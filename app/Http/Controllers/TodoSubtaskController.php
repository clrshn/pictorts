<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\TodoSubtask;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class TodoSubtaskController extends Controller
{
    public function store(Request $request, Todo $todo)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $subtask = $todo->subtasks()->create([
            'title' => $validated['title'],
            'position' => ($todo->subtasks()->max('position') ?? 0) + 1,
        ]);

        app(ActivityLogService::class)->log(
            $todo,
            'subtask_added',
            'Subtask added',
            auth()->user()?->name . ' added a subtask.',
            ['subtask' => $subtask->title]
        );

        return back()->with('success', 'Subtask added successfully.');
    }

    public function update(Request $request, Todo $todo, TodoSubtask $subtask)
    {
        abort_unless($subtask->todo_id === $todo->id, 404);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'is_completed' => 'nullable|boolean',
        ]);

        if (array_key_exists('title', $validated) && $validated['title'] !== null) {
            $subtask->title = $validated['title'];
        }

        if (array_key_exists('is_completed', $validated)) {
            $subtask->is_completed = (bool) $validated['is_completed'];
            $subtask->completed_at = $subtask->is_completed ? now() : null;
        }

        $subtask->save();

        app(ActivityLogService::class)->log(
            $todo,
            'subtask_updated',
            'Subtask updated',
            auth()->user()?->name . ' updated a subtask.',
            [
                'subtask' => $subtask->title,
                'completed' => $subtask->is_completed,
            ]
        );

        return $request->wantsJson()
            ? response()->json(['success' => true])
            : back()->with('success', 'Subtask updated successfully.');
    }

    public function destroy(Todo $todo, TodoSubtask $subtask)
    {
        abort_unless($subtask->todo_id === $todo->id, 404);
        abort_unless(auth()->user()?->isAdmin(), 403);

        $title = $subtask->title;
        $subtask->delete();

        app(ActivityLogService::class)->log(
            $todo,
            'subtask_removed',
            'Subtask removed',
            auth()->user()?->name . ' removed a subtask.',
            ['subtask' => $title]
        );

        return back()->with('success', 'Subtask removed successfully.');
    }
}
