<x-app-layout>
    <x-slot name="header">
        <h1>Todo List</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / Todo List</div>
    </x-slot>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <!-- Search and Filter -->
    <div class="filter-box">
        <h3>Search & Filter</h3>
        
        <!-- Active Filters Indicator -->
        @if(request()->hasAny(['status', 'priority', 'search']))
            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; justify-content:flex-end; margin-bottom:12px;">
                <span style="font-weight:600; color:#666;">Active Filters:</span>
                @if(request('status'))
                    <span class="badge" style="background:#1976d2; color:white; padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                        Status: {{ request('status') }}
                        <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" style="color:white; text-decoration:none; font-weight:bold; cursor:pointer;" title="Remove status filter">×</a>
                    </span>
                @endif
                @if(request('priority'))
                    <span class="badge" style="background:#1976d2; color:white; padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                        Priority: {{ request('priority') }}
                        <a href="{{ request()->fullUrlWithQuery(['priority' => null]) }}" style="color:white; text-decoration:none; font-weight:bold; cursor:pointer;" title="Remove priority filter">×</a>
                    </span>
                @endif
                @if(request('search'))
                    <span class="badge" style="background:#1976d2; color:white; padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                        Search: {{ request('search') }}
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" style="color:white; text-decoration:none; font-weight:bold; cursor:pointer;" title="Remove search filter">×</a>
                    </span>
                @endif
                <a href="{{ route('todos.index') }}" style="color:#1976d2; cursor:pointer; font-weight:600; text-decoration:underline;">Clear All</a>
            </div>
        @endif
        
        <form method="GET" action="{{ route('todos.index') }}">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:12px;">
                <div class="form-group" style="margin:0">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="form-group" style="margin:0">
                    <label>Priority</label>
                    <select name="priority" class="form-control">
                        <option value="">All</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="form-group" style="margin:0">
                    <label>Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search todos...">
                </div>
                <div class="form-group" style="margin:0; display:flex; align-items:flex-end; gap:8px;">
                    <button type="submit" class="btn-red"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('todos.index') }}" class="btn-gray">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Todo List -->
    <div class="table-card">
        <div class="table-header">
            <h3>My Todos</h3>
            <a href="{{ route('todos.create') }}" class="btn-red"><i class="fas fa-plus"></i> Add Todo</a>
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>TASK</th>
                        <th>PRIORITY</th>
                        <th>STATUS</th>
                        <th>DUE DATE</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todos as $todo)
                        <tr style="{{ $todo->isOverdue() ? 'background:#fff5f5;' : '' }}">
                            <td style="text-align:left; max-width:300px;">
                                <div style="font-weight:600; color:#333; margin-bottom:4px;">{{ $todo->title }}</div>
                                @if($todo->description)
                                    <div style="font-size:12px; color:#666; line-height:1.4;">{{ Str::limit($todo->description, 100) }}</div>
                                @endif
                                @if($todo->isOverdue())
                                    <div style="font-size:11px; color:#e74c3c; margin-top:4px;">
                                        <i class="fas fa-exclamation-triangle"></i> Overdue
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background:{{ $todo->priority_color }}; color:white;">
                                    {{ $todo->priority_badge }}
                                </span>
                            </td>
                            <td>
                                <select class="status-select" onchange="quickUpdateStatus({{ $todo->id }}, this.value)" style="font-size:12px; padding:4px; border-radius:4px; border:1px solid #ddd;">
                                    <option value="pending" {{ $todo->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $todo->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $todo->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </td>
                            <td>
                                @if($todo->due_date)
                                    <span style="{{ $todo->isOverdue() ? 'color:#e74c3c; font-weight:600;' : '' }}">
                                        {{ $todo->due_date->format('M d, Y') }}
                                    </span>
                                @else
                                    <span style="color:#999;">No due date</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('todos.edit', $todo) }}" class="btn-blue" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('todos.destroy', $todo) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this todo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:40px; color:#999;">
                                <i class="fas fa-tasks" style="font-size:48px; margin-bottom:16px; display:block;"></i>
                                <div>No todos found. Create your first todo!</div>
                                <a href="{{ route('todos.create') }}" class="btn-red" style="margin-top:16px;">
                                    <i class="fas fa-plus"></i> Add Todo
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($todos->hasPages())
            <div style="padding:20px; text-align:center;">
                {{ $todos->links() }}
            </div>
        @endif
    </div>
</x-app-layout>

<script>
function quickUpdateStatus(todoId, newStatus) {
    fetch(`/todos/${todoId}/quick-update`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const row = event.target.closest('tr');
            const statusCell = row.querySelector('td:nth-child(3) select');
            const statusBadge = statusCell.closest('td').querySelector('.badge');
            
            // Update status badge if needed
            if (newStatus === 'completed') {
                row.style.textDecoration = 'line-through';
                row.style.opacity = '0.7';
            } else {
                row.style.textDecoration = 'none';
                row.style.opacity = '1';
            }
        }
    })
    .catch(error => {
        console.error('Error updating status:', error);
    });
}
</script>

<style>
.status-select {
    cursor: pointer;
    transition: all 0.2s;
}

.status-select:hover {
    border-color: #c0392b;
    box-shadow: 0 0 0 2px rgba(192,57,43,0.1);
}
</style>
