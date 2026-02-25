<x-app-layout>
    <x-slot name="header">
        <h1>Create Todo</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('todos.index') }}">Todo List</a> / Create Todo</div>
    </x-slot>

    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px;">
            <i class="fas fa-plus"></i> CREATE NEW TODO
        </div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('todos.store') }}">
                @csrf

                <div class="form-group">
                    <label>Title <span style="color:#c0392b">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Enter todo title...">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Add more details about this todo...">{{ old('description') }}</textarea>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Priority <span style="color:#c0392b">*</span></label>
                        <select name="priority" class="form-control" required>
                            <option value="">Select Priority</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}" min="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>

                <div style="display:flex; gap:12px; margin-top:24px;">
                    <button type="submit" class="btn-red">
                        <i class="fas fa-save"></i> Create Todo
                    </button>
                    <a href="{{ route('todos.index') }}" class="btn-gray">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
