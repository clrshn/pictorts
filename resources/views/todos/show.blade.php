<x-app-layout>
    <x-slot name="header">
        <h1>Todo Details</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('todos.index') }}">Todo List</a> / Todo Details</div>
    </x-slot>

    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px; display:flex; justify-content:space-between; align-items:center;">
            <span><i class="fas fa-tasks"></i> Todo Details</span>
            <div class="detail-header-actions">
                @include('components.pin-toggle', ['record' => $todo, 'subjectType' => 'todo'])
                <a href="{{ request()->fullUrlWithQuery(['export' => 'print']) }}" target="_blank" class="btn-blue"><i class="fas fa-print"></i> Print</a>
                <a href="{{ route('todos.edit', $todo) }}" class="btn-orange"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ route('todos.index') }}" class="btn-gray"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px 32px; font-size:13px;">
                <div style="grid-column:span 2; border-left:3px solid #27ae60; padding-left:12px; margin-bottom:8px;">
                    <div><strong>Title:</strong> {{ $todo->title }}</div>
                </div>

                <div style="border-left:3px solid #3498db; padding-left:12px;">
                    <div style="margin-bottom:8px;"><strong>Priority:</strong> <span class="badge" style="background:{{ $todo->priority_color }}; color:white;">{{ $todo->priority_badge }}</span></div>
                    <div><strong>Status:</strong> <span class="badge" style="background:{{ $todo->status_color }}; color:white;">{{ $todo->status_badge }}</span></div>
                </div>

                <div style="border-left:3px solid #f39c12; padding-left:12px;">
                    <div><strong>DATE ADDED:</strong> {{ $todo->date_added?->format('F d, Y') ?? 'No date added' }}</div>
                    <div style="margin-top:8px;"><strong>Assigned To:</strong> {{ $todo->assigned_to ?? 'Unassigned' }}</div>
                </div>

                @if($todo->description)
                    <div style="grid-column:span 2; border-left:3px solid #8e44ad; padding-left:12px; margin-top:8px;">
                        <div><strong>Description:</strong></div>
                        <div style="margin-top:4px; padding:12px; background:#f8f9fa; border-radius:4px; line-height:1.5;">
                            {{ $todo->description }}
                        </div>
                    </div>
                @endif

                <div style="grid-column:span 2; border-left:3px solid #e74c3c; padding-left:12px; margin-top:8px;">
                    <div><strong>Remarks:</strong> {{ $todo->remarks ?? '—' }}</div>
                </div>

                <div style="grid-column:span 2; border-left:3px solid #2563eb; padding-left:12px; margin-top:8px;">
                    <div><strong>Recurring:</strong> {{ $todo->is_recurring ? 'Yes' : 'No' }}</div>
                    @if($todo->is_recurring)
                        <div style="margin-top:6px;"><strong>Frequency:</strong> {{ ucfirst($todo->recurrence_frequency ?? 'weekly') }} every {{ $todo->recurrence_interval ?? 1 }}</div>
                        <div style="margin-top:6px;"><strong>Repeat Until:</strong> {{ $todo->recurrence_end_date?->format('F d, Y') ?? 'No end date' }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="table-card" style="margin-top:20px;">
        <div class="table-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:8px;">
                <i class="fas fa-list-check" style="color:#8b0000;"></i>
                <h3 style="margin:0; color:#333;">Checklist / Subtasks</h3>
            </div>
            <span style="font-size:12px; color:#64748b; font-weight:600;">{{ $todo->completion_percent }}% complete</span>
        </div>
        <div style="padding:18px 20px;">
            <form method="POST" action="{{ route('todos.subtasks.store', $todo) }}" style="display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap;">
                @csrf
                <input type="text" name="title" class="form-control" placeholder="Add a subtask..." style="flex:1; min-width:240px;" required>
                <button type="submit" class="btn-blue"><i class="fas fa-plus"></i> Add Subtask</button>
            </form>

            @if($todo->subtasks->isEmpty())
                <div style="padding:18px; border:1px dashed #cbd5e1; border-radius:12px; text-align:center; color:#64748b; font-size:13px;">
                    No subtasks yet. Add a checklist if this task has smaller steps.
                </div>
            @else
                <div style="display:flex; flex-direction:column; gap:10px;">
                    @foreach($todo->subtasks as $subtask)
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 14px; border-radius:12px; border:1px solid rgba(226,232,240,0.92); background:{{ $subtask->is_completed ? 'linear-gradient(135deg,#ecfdf5 0%,#f0fdf4 100%)' : '#fff' }};">
                            <form method="POST" action="{{ route('todos.subtasks.update', [$todo, $subtask]) }}" style="display:flex; align-items:center; gap:12px; flex:1;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_completed" value="{{ $subtask->is_completed ? 0 : 1 }}">
                                <button type="submit" style="border:none; background:none; cursor:pointer; color:{{ $subtask->is_completed ? '#16a34a' : '#94a3b8' }}; font-size:18px;">
                                    <i class="fas {{ $subtask->is_completed ? 'fa-circle-check' : 'fa-circle' }}"></i>
                                </button>
                                <div style="font-size:13px; color:#334155; text-decoration:{{ $subtask->is_completed ? 'line-through' : 'none' }};">
                                    {{ $subtask->title }}
                                </div>
                            </form>
                            <form method="POST" action="{{ route('todos.subtasks.destroy', [$todo, $subtask]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger" style="padding:6px 10px;"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @include('components.collaboration-panel', [
        'record' => $todo,
        'subjectType' => 'todo',
    ])
</x-app-layout>
