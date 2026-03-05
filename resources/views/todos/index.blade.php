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
                    <span class="badge" style="background:#6c757d; color:white; padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                        Status: {{ request('status') }}
                        <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer; font-weight:bold;" title="Remove status filter">×</a>
                    </span>
                @endif
                @if(request('priority'))
                    <span class="badge" style="background:#ffc107; color:black; padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                        Priority: {{ request('priority') }}
                        <a href="{{ request()->fullUrlWithQuery(['priority' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer; font-weight:bold;" title="Remove priority filter">×</a>
                    </span>
                @endif
                @if(request('search'))
                    <span class="badge" style="background:#1976d2; color:white; padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                        Search: {{ request('search') }}
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer; font-weight:bold;" title="Remove search filter">×</a>
                    </span>
                @endif
                <a href="{{ route('todos.index') }}" class="btn btn-sm btn-outline-secondary">Clear All</a>
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
                                <form action="{{ route('todos.destroy', $todo) }}" method="POST" style="display:inline;" id="deleteForm-{{ $todo->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-danger" title="Delete" onclick="confirmDelete({{ $todo->id }}, '{{ $todo->title }}')"><i class="fas fa-trash"></i></button>
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

<!-- Notification Container -->
<div class="notification-container" id="notificationContainer"></div>

<style>
    /* Modern Notification System */
    .notification-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        pointer-events: none;
    }

    .notification {
        background: #fff;
        border-radius: 8px;
        padding: 16px 20px;
        margin-bottom: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-left: 4px solid #e74c3c;
        min-width: 300px;
        max-width: 400px;
        pointer-events: all;
        animation: slideInRight 0.3s ease-out;
        position: relative;
        overflow: hidden;
    }

    .notification.success {
        border-left-color: #27ae60;
    }

    .notification.warning {
        border-left-color: #f39c12;
    }

    .notification.info {
        border-left-color: #3498db;
    }

    .notification-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .notification-title {
        font-weight: 600;
        font-size: 14px;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .notification-close {
        background: none;
        border: none;
        color: #7f8c8d;
        font-size: 18px;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .notification-close:hover {
        background: #f8f9fa;
        color: #2c3e50;
    }

    .notification-message {
        color: #555;
        font-size: 13px;
        line-height: 1.4;
    }

    .notification-actions {
        margin-top: 12px;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .notification-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .notification-btn-confirm {
        background: #e74c3c;
        color: white;
    }

    .notification-btn-confirm:hover {
        background: #c0392b;
    }

    .notification-btn-cancel {
        background: #ecf0f1;
        color: #555;
    }

    .notification-btn-cancel:hover {
        background: #bdc3c7;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .notification.removing {
        animation: slideOutRight 0.3s ease-out forwards;
    }
</style>

<script>
    // Modern Notification System - Local implementation
    function showNotification(options) {
        const {
            type = 'info',
            title = 'Notification',
            message = '',
            duration = 5000,
            actions = null,
            icon = null
        } = options;

        const container = document.getElementById('notificationContainer');
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;

        // Determine icon based on type
        let iconHtml = '';
        if (icon) {
            iconHtml = `<i class="${icon}"></i>`;
        } else {
            switch(type) {
                case 'success':
                    iconHtml = '✓';
                    break;
                case 'warning':
                    iconHtml = '⚠';
                    break;
                case 'danger':
                    iconHtml = '✗';
                    break;
                default:
                    iconHtml = 'ℹ';
            }
        }

        let actionsHtml = '';
        if (actions && actions.length > 0) {
            actionsHtml = '<div class="notification-actions">';
            actions.forEach(action => {
                actionsHtml += `<button class="notification-btn ${action.class}" onclick="${action.onclick}">${action.text}</button>`;
            });
            actionsHtml += '</div>';
        }

        notification.innerHTML = `
            <div class="notification-header">
                <div class="notification-title">${iconHtml} ${title}</div>
                <button class="notification-close" onclick="removeNotification(this)">&times;</button>
            </div>
            <div class="notification-message">${message}</div>
            ${actionsHtml}
        `;

        container.appendChild(notification);

        // Auto-remove after duration
        if (duration > 0) {
            setTimeout(() => {
                removeNotification(notification.querySelector('.notification-close'));
            }, duration);
        }

        return notification;
    }

    function removeNotification(element) {
        const notification = element.closest('.notification');
        if (notification) {
            notification.classList.add('removing');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }
    }

    // Confirmation dialog function
    function showConfirmDialog(options) {
        const {
            title = 'Confirm Action',
            message = 'Are you sure you want to proceed?',
            confirmText = 'Confirm',
            cancelText = 'Cancel',
            confirmClass = 'notification-btn-confirm',
            onConfirm = null,
            onCancel = null
        } = options;

        return new Promise((resolve) => {
            const notification = showNotification({
                type: 'warning',
                title: title,
                message: message,
                duration: 0, // Don't auto-close
                actions: [
                    {
                        text: cancelText,
                        class: 'notification-btn-cancel',
                        onclick: `removeNotification(this.closest('.notification').querySelector('.notification-close')); confirmDialogCancel();`
                    },
                    {
                        text: confirmText,
                        class: confirmClass,
                        onclick: `removeNotification(this.closest('.notification').querySelector('.notification-close')); confirmDialogConfirm();`
                    }
                ]
            });

            window.confirmDialogConfirm = () => {
                if (onConfirm) onConfirm();
                resolve(true);
                delete window.confirmDialogConfirm;
                delete window.confirmDialogCancel;
            };

            window.confirmDialogCancel = () => {
                if (onCancel) onCancel();
                resolve(false);
                delete window.confirmDialogConfirm;
                delete window.confirmDialogCancel;
            };
        });
    }

    function confirmDelete(todoId, todoTitle) {
        console.log('confirmDelete called with:', todoId, todoTitle); // Debug log
        
        showConfirmDialog({
            title: 'Delete Todo',
            message: `Are you sure you want to delete this todo?<br><br><strong>Todo:</strong> ${todoTitle}<br><strong>This action cannot be undone!</strong>`,
            confirmText: 'Delete',
            cancelText: 'Cancel',
            confirmClass: 'notification-btn-confirm',
            onConfirm: function() {
                console.log('Delete confirmed, submitting form:', todoId); // Debug log
                const form = document.getElementById(`deleteForm-${todoId}`);
                if (form) {
                    form.submit();
                } else {
                    console.error('Form not found:', `deleteForm-${todoId}`);
                }
            }
        });
    }

    // Test function
    function testNotification() {
        showNotification({
            type: 'info',
            title: 'Test Notification',
            message: 'This is a test notification to verify the system is working.',
            duration: 3000
        });
    }

    // Auto-test on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Todos page loaded, notification system ready');
        // Uncomment to test automatically:
        // setTimeout(testNotification, 1000);
    });
</script>
