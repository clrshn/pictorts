<x-app-layout>
    <x-slot name="header">
        <h1>Todo Details</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('todos.index') }}">Todo List</a> / Todo Details</div>
    </x-slot>

    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px; display:flex; justify-content:space-between; align-items:center;">
            <span><i class="fas fa-tasks"></i> Todo Details</span>
            <div class="detail-header-actions">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'print']) }}" target="_blank" class="btn-blue"><i class="fas fa-print"></i> Print</a>
                <a href="{{ route('todos.edit', $todo) }}" class="btn-orange"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ route('todos.index') }}" class="btn-gray"><i class="fas fa-arrow-left"></i> Back</a>
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
                    <div><strong>DATE ADDED:</strong> 
                        @if($todo->date_added)
                            <span>
                                {{ $todo->date_added->format('F d, Y') }}
                            </span>
                        @else
                            <span style="color:#999;">No date added</span>
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
        </div>
    </div>
</x-app-layout>
