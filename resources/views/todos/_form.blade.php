@php
    $todo = $todo ?? null;
    $formMode = $formMode ?? ($todo ? 'edit' : 'create');
    $isEdit = $formMode === 'edit';
    $isModal = $isModal ?? false;
    $formAction = $formAction ?? ($isEdit ? route('todos.update', $todo) : route('todos.store'));
    $cancelUrl = $cancelUrl ?? route('todos.index');
    $modalId = $modalId ?? null;
    $returnUrl = $returnUrl ?? ($isModal ? request()->fullUrl() : route('todos.index'));
@endphp

@if($errors->any())
    <div class="alert-error" style="margin-bottom:16px;">
        <ul style="margin:0; padding-left:18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div style="padding:24px;">
    <form method="POST" action="{{ $formAction }}">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <input type="hidden" name="modal_mode" value="{{ $formMode }}">
        <input type="hidden" name="modal_record_id" value="{{ $todo?->id }}">
        <input type="hidden" name="return_to" value="{{ $returnUrl }}">

        <div class="form-group">
            <label>Task <span style="color:#c0392b">*</span></label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $todo?->title) }}" required placeholder="Enter task description...">
        </div>

        <div class="form-group">
            <label>What to-do</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Add more details about this todo...">{{ old('description', $todo?->description) }}</textarea>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <div class="form-group">
                <label>Priority <span style="color:#c0392b">*</span></label>
                <select name="priority" class="form-control" required>
                    <option value="top" {{ old('priority', $todo?->priority) === 'top' ? 'selected' : '' }}>Top</option>
                    <option value="high" {{ old('priority', $todo?->priority) === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ old('priority', $todo?->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ old('priority', $todo?->priority) === 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="form-group">
                <label>{{ $isEdit ? 'Status' : 'Assigned To' }}@if($isEdit) <span style="color:#c0392b">*</span>@endif</label>
                @if($isEdit)
                    <select name="status" class="form-control" required>
                        <option value="pending" {{ old('status', $todo?->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="on-going" {{ old('status', $todo?->status) === 'on-going' ? 'selected' : '' }}>On-going</option>
                        <option value="done" {{ old('status', $todo?->status) === 'done' ? 'selected' : '' }}>Done</option>
                        <option value="cancelled" {{ old('status', $todo?->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                @else
                    <input type="text" name="assigned_to" class="form-control" value="{{ old('assigned_to', $todo?->assigned_to) }}" list="todoAssignedPeople" placeholder="Enter one or more names">
                @endif
            </div>
        </div>

        @if($isEdit)
            <div class="form-group">
                <label>Assigned To</label>
                <input type="text" name="assigned_to" class="form-control" value="{{ old('assigned_to', $todo?->assigned_to) }}" list="todoAssignedPeople" placeholder="Enter one or more names">
                <small style="color:#64748b;">You can keep the current assignee, add another name, or enter multiple people manually.</small>
            </div>
        @else
            <div class="form-group">
                <small style="color:#64748b;">You can type any person or multiple names, then reuse suggestions below.</small>
            </div>
        @endif

        <datalist id="todoAssignedPeople">
            @foreach($assignedToOptions as $person)
                <option value="{{ $person }}"></option>
            @endforeach
        </datalist>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <div class="form-group">
                <label>Date Added</label>
                <input type="date" name="date_added" class="form-control" value="{{ old('date_added', $todo?->date_added ? $todo->date_added->format('Y-m-d') : '') }}">
            </div>
            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $todo?->due_date ? $todo->due_date->format('Y-m-d') : '') }}" min="{{ now()->format('Y-m-d') }}">
            </div>
        </div>

        <div class="form-group">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control" rows="3" placeholder="Add any additional remarks...">{{ old('remarks', $todo?->remarks) }}</textarea>
        </div>

        <div style="margin-top:20px; padding:16px; border-radius:14px; border:1px solid rgba(148,163,184,0.22); background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%);">
            <div style="font-size:13px; font-weight:700; color:#334155; margin-bottom:12px;">Recurring Task</div>
            <label style="display:flex; align-items:center; gap:10px; margin-bottom:14px; font-size:13px; color:#475569;">
                <input type="checkbox" name="is_recurring" value="1" {{ old('is_recurring', $todo?->is_recurring) ? 'checked' : '' }}>
                <span>{{ $isEdit ? 'Keep this as a recurring task' : 'Create this as a recurring task' }}</span>
            </label>
            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label>Frequency</label>
                    <select name="recurrence_frequency" class="form-control">
                        <option value="daily" {{ old('recurrence_frequency', $todo?->recurrence_frequency) === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ old('recurrence_frequency', $todo?->recurrence_frequency ?? 'weekly') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ old('recurrence_frequency', $todo?->recurrence_frequency) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ old('recurrence_frequency', $todo?->recurrence_frequency) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Repeat Every</label>
                    <input type="number" name="recurrence_interval" class="form-control" min="1" max="30" value="{{ old('recurrence_interval', $todo?->recurrence_interval ?? 1) }}">
                </div>
                <div class="form-group">
                    <label>Repeat Until</label>
                    <input type="date" name="recurrence_end_date" class="form-control" value="{{ old('recurrence_end_date', $todo?->recurrence_end_date?->format('Y-m-d')) }}">
                </div>
            </div>
        </div>

        @if(!$isModal && $isEdit)
            <div style="background:#f8f9fa; border-left:4px solid #3498db; padding:12px; margin-top:20px; margin-bottom:20px; border-radius:4px;">
                <div style="font-size:12px; color:#666; margin-bottom:4px;">Created:</div>
                <div style="font-size:13px; color:#333;">{{ $todo->created_at->format('F d, Y h:i A') }}</div>
                @if($todo->updated_at != $todo->created_at)
                    <div style="font-size:12px; color:#666; margin-top:8px; margin-bottom:4px;">Last Updated:</div>
                    <div style="font-size:13px; color:#333;">{{ $todo->updated_at->format('F d, Y h:i A') }}</div>
                @endif
            </div>
        @endif

        <div style="display:flex; gap:12px; margin-top:24px; flex-wrap:wrap; justify-content:{{ $isModal ? 'flex-end' : 'flex-start' }};">
            <button type="submit" class="btn-red">
                <i class="fas fa-save"></i> {{ $isEdit ? 'Update Todo' : 'Create Todo' }}
            </button>
            @if(!$isModal || !$modalId)
                @if($isEdit)
                    <a href="{{ route('todos.show', $todo) }}" class="btn-blue">
                        <i class="fas fa-eye"></i> View
                    </a>
                @endif
                <a href="{{ $cancelUrl }}" class="btn-gray">
                    <i class="fas fa-arrow-left"></i> {{ $isEdit ? 'Back' : 'Cancel' }}
                </a>
            @endif
        </div>
    </form>
</div>
