<x-app-layout>
    <x-slot name="header">
        <h1>Create Todo</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('todos.index') }}">Todo List</a> / Create Todo</div>
    </x-slot>

    @include('components.notifications')

    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px;">
            <i class="fas fa-plus"></i> CREATE NEW TODO
        </div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('todos.store') }}">
                @csrf

                <div class="form-group">
                    <label>Task <span style="color:#c0392b">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Enter task description...">
                </div>

                <div class="form-group">
                    <label>What to-do <span style="color:#c0392b">*</span></label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Add more details about this todo...">{{ old('description') }}</textarea>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Priority <span style="color:#c0392b">*</span></label>
                        <select name="priority" class="form-control" required>
                            <option value="top" {{ old('priority') === 'top' ? 'selected' : '' }}>Top</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Assigned To</label>
                        <select name="assigned_to" class="form-control">
                            <option value="">Select Person</option>
                            <option value="ADMIN UNIT" {{ old('assigned_to') === 'ADMIN UNIT' ? 'selected' : '' }}>ADMIN UNIT</option>
                            <option value="CLYDE" {{ old('assigned_to') === 'CLYDE' ? 'selected' : '' }}>CLYDE</option>
                            <option value="MARGIE" {{ old('assigned_to') === 'MARGIE' ? 'selected' : '' }}>MARGIE</option>
                            <option value="MELETH" {{ old('assigned_to') === 'MELETH' ? 'selected' : '' }}>MELETH</option>
                            <option value="JACKIE" {{ old('assigned_to') === 'JACKIE' ? 'selected' : '' }}>JACKIE</option>
                            <option value="PATRICK" {{ old('assigned_to') === 'PATRICK' ? 'selected' : '' }}>PATRICK</option>
                            <option value="MITCH" {{ old('assigned_to') === 'MITCH' ? 'selected' : '' }}>MITCH</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>DATE ADDED</label>
                    <input type="date" name="date_added" class="form-control" value="{{ old('date_added') }}">
                </div>

                <div class="form-group">
                    <label>Due Date</label>
                    <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}" min="{{ now()->format('Y-m-d') }}">
                </div>

                <div class="form-group">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control" rows="3" placeholder="Add any additional remarks...">{{ old('remarks') }}</textarea>
                </div>

                <div style="margin-top:20px; padding:16px; border-radius:14px; border:1px solid rgba(148,163,184,0.22); background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%);">
                    <div style="font-size:13px; font-weight:700; color:#334155; margin-bottom:12px;">Recurring Task</div>
                    <label style="display:flex; align-items:center; gap:10px; margin-bottom:14px; font-size:13px; color:#475569;">
                        <input type="checkbox" name="is_recurring" value="1" {{ old('is_recurring') ? 'checked' : '' }}>
                        <span>Create this as a recurring task</span>
                    </label>
                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label>Frequency</label>
                            <select name="recurrence_frequency" class="form-control">
                                <option value="daily" {{ old('recurrence_frequency') === 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ old('recurrence_frequency', 'weekly') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ old('recurrence_frequency') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly" {{ old('recurrence_frequency') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Repeat Every</label>
                            <input type="number" name="recurrence_interval" class="form-control" min="1" max="30" value="{{ old('recurrence_interval', 1) }}">
                        </div>
                        <div class="form-group">
                            <label>Repeat Until</label>
                            <input type="date" name="recurrence_end_date" class="form-control" value="{{ old('recurrence_end_date') }}">
                        </div>
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
