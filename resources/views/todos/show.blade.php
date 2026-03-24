<x-app-layout>
    <x-slot name="header">
        <h1>Todo Details</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('todos.index') }}">Todo List</a> / Todo Details</div>
    </x-slot>

    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px; display:flex; justify-content:space-between; align-items:center;">
            <span><i class="fas fa-tasks"></i> Todo Details</span>
            <div style="display:flex; gap:8px;">
                <a href="{{ route('todos.edit', $todo) }}" class="btn-orange" style="padding:4px 12px;"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ route('todos.index') }}" class="btn-gray" style="padding:4px 12px;"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px 32px; font-size:13px;">
                <!-- Title (Full Width, Top) -->
                <div style="grid-column:span 2; border-left:3px solid #27ae60; padding-left:12px; margin-bottom:8px;">
                    <div><strong>Title:</strong> {{ $todo->title }}</div>
                </div>
                
                <!-- Task Information -->
                <div style="border-left:3px solid #3498db; padding-left:12px;">
                    <div style="margin-bottom:8px;"><strong>Priority:</strong> <span class="badge" style="background:{{ $todo->priority_color }}; color:white;">{{ $todo->priority_badge }}</span></div>
                    <div><strong>Status:</strong> <span class="badge" style="background:{{ $todo->status_color }}; color:white;">{{ $todo->status_badge }}</span></div>
                </div>
                
                <!-- Schedule Information -->
                <div style="border-left:3px solid #f39c12; padding-left:12px;">
                    <div><strong>Due Date:</strong> 
                        @if($todo->due_date)
                            <span style="{{ $todo->isOverdue() ? 'color:#e74c3c; font-weight:600;' : '' }}">
                                {{ $todo->due_date->format('F d, Y') }}
                                @if($todo->isOverdue())
                                    <span style="margin-left:8px; font-size:11px;"><i class="fas fa-exclamation-triangle"></i> Overdue</span>
                                @endif
                            </span>
                        @else
                            <span style="color:#999;">No due date</span>
                        @endif
                    </div>
                    <div style="margin-top:8px;"><strong>Assigned To:</strong> {{ $todo->assigned_to ?? 'Unassigned' }}</div>
                </div>
                
                <!-- Description (Full Width) -->
                @if($todo->description)
                    <div style="grid-column:span 2; border-left:3px solid #8e44ad; padding-left:12px; margin-top:8px;">
                        <div><strong>Description:</strong></div>
                        <div style="margin-top:4px; padding:12px; background:#f8f9fa; border-radius:4px; line-height:1.5;">
                            {{ $todo->description }}
                        </div>
                    </div>
                @endif
                
                <!-- Remarks (Full Width) -->
                <div style="grid-column:span 2; border-left:3px solid #e74c3c; padding-left:12px; margin-top:8px;">
                    <div><strong>Remarks:</strong> {{ $todo->remarks ?? '—' }}</div>
                </div>
            </div>
            <div style="margin-top:16px; padding-top:16px; border-top:1px solid #e5e7eb;">
                <div style="font-size:12px; color:#666; margin-bottom:4px;">Created:</div>
                <div style="font-size:13px; color:#333;">{{ $todo->created_at->format('F d, Y h:i A') }}</div>
                @if($todo->updated_at != $todo->created_at)
                    <div style="font-size:12px; color:#666; margin-top:8px; margin-bottom:4px;">Last Updated:</div>
                    <div style="font-size:13px; color:#333;">{{ $todo->updated_at->format('F d, Y h:i A') }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
