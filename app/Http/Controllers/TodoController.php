<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Todo::forUser();

        // Filter by status
        if ($request->filled('status') && $request->status !== 'ALL') {
            $query->byStatus($request->status);
        }

        // Filter by priority
        if ($request->filled('priority') && $request->priority !== 'ALL') {
            $query->byPriority($request->priority);
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
        $todos = $query->orderBy('due_date', 'asc')
                      ->orderByRaw("FIELD(priority, 'top', 'high', 'medium', 'low') ASC")
                      ->orderBy('created_at', 'desc')
                      ->paginate(15);

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
        ]);

        $validated['user_id'] = auth()->id();

        Todo::create($validated);

        return redirect()->route('todos.index')
            ->with('success', 'Todo created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        return view('todos.show', compact('todo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        return view('todos.edit', compact('todo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,top',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date|after_or_equal:today',
            'assigned_to' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $todo->update($validated);

        return redirect()->route('todos.index')
            ->with('updated', 'Todo updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        $todo->delete();

        return redirect()->route('todos.index')
            ->with('deleted', 'Todo deleted successfully!');
    }

    /**
     * Quick update todo status or assigned_to
     */
    public function quickUpdate(Request $request, Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'nullable|in:pending,in_progress,completed',
            'assigned_to' => 'nullable|string|max:255',
            'priority' => 'nullable|in:low,medium,high,top'
        ]);

        $todo->update($validated);

        return response()->json(['success' => true, 'status' => $todo->status, 'assigned_to' => $todo->assigned_to]);
    }
}
