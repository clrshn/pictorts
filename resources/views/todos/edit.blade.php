<x-app-layout>
    <x-slot name="header">
        <h1>Edit Todo</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('todos.index') }}">Todo List</a> / Edit Todo</div>
    </x-slot>

    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px;">
            <i class="fas fa-edit"></i> EDIT TODO
        </div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('todos.update', $todo) }}">
                @csrf @method('PUT')

                <div class="form-group">
                    <label>Title <span style="color:#c0392b">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $todo->title) }}" required placeholder="Enter todo title...">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Add more details about this todo...">{{ old('description', $todo->description) }}</textarea>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Priority <span style="color:#c0392b">*</span></label>
                        <select name="priority" class="form-control" required>
                            <option value="">Select Priority</option>
                            <option value="top" {{ old('priority', $todo->priority) === 'top' ? 'selected' : '' }}>Top</option>
                            <option value="high" {{ old('priority', $todo->priority) === 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ old('priority', $todo->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ old('priority', $todo->priority) === 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status <span style="color:#c0392b">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="pending" {{ old('status', $todo->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status', $todo->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $todo->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Assigned To</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">Select Person</option>
                        <option value="ADMIN UNIT" {{ old('assigned_to', $todo->assigned_to) === 'ADMIN UNIT' ? 'selected' : '' }}>ADMIN UNIT</option>
                        <option value="CLYDE" {{ old('assigned_to', $todo->assigned_to) === 'CLYDE' ? 'selected' : '' }}>CLYDE</option>
                        <option value="MARGIE" {{ old('assigned_to', $todo->assigned_to) === 'MARGIE' ? 'selected' : '' }}>MARGIE</option>
                        <option value="MELETH" {{ old('assigned_to', $todo->assigned_to) === 'MELETH' ? 'selected' : '' }}>MELETH</option>
                        <option value="JACKIE" {{ old('assigned_to', $todo->assigned_to) === 'JACKIE' ? 'selected' : '' }}>JACKIE</option>
                        <option value="PATRICK" {{ old('assigned_to', $todo->assigned_to) === 'PATRICK' ? 'selected' : '' }}>PATRICK</option>
                        <option value="MITCH" {{ old('assigned_to', $todo->assigned_to) === 'MITCH' ? 'selected' : '' }}>MITCH</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Due Date</label>
                    <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $todo->due_date ? $todo->due_date->format('Y-m-d') : '') }}" min="{{ now()->format('Y-m-d') }}">
                </div>

                <div class="form-group">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control" rows="3" placeholder="Add any additional remarks...">{{ old('remarks', $todo->remarks) }}</textarea>
                </div>

                <div style="background:#f8f9fa; border-left:4px solid #3498db; padding:12px; margin-bottom:20px; border-radius:4px;">
                    <div style="font-size:12px; color:#666; margin-bottom:4px;">Created:</div>
                    <div style="font-size:13px; color:#333;">{{ $todo->created_at->format('F d, Y h:i A') }}</div>
                    @if($todo->updated_at != $todo->created_at)
                        <div style="font-size:12px; color:#666; margin-top:8px; margin-bottom:4px;">Last Updated:</div>
                        <div style="font-size:13px; color:#333;">{{ $todo->updated_at->format('F d, Y h:i A') }}</div>
                    @endif
                </div>

                <div style="display:flex; gap:12px; margin-top:24px;">
                    <button type="submit" class="btn-red">
                        <i class="fas fa-save"></i> Update Todo
                    </button>
                    <a href="{{ route('todos.show', $todo) }}" class="btn-blue">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('todos.index') }}" class="btn-gray">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
