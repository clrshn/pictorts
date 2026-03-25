<x-app-layout>
    <x-slot name="header">
        <h1>Task Monitoring</h1>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Home</a> / Task Monitoring
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 16px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 16px;">
            {{ session('error') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning" style="margin-bottom: 16px;">
            {{ session('warning') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info" style="margin-bottom: 16px;">
            {{ session('info') }}
        </div>
    @endif

    <div class="filter-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="margin:0;">Search Filter</h3>

            @if(request()->hasAny(['status','priority','assigned_to','search']))
                <div style="display:flex; gap:4px; flex-wrap:wrap; align-items:center;">
                    <span style="color:#666; font-size:15px;">Active Filters:</span>
                    @foreach(['status','priority','assigned_to','search'] as $filter)
                        @if(request($filter))
                            <span class="badge" style="background:#1976d2; color:white; padding:1px 5px; border-radius:2px; display:flex; align-items:center; gap:3px; font-size:12px; white-space:nowrap;">
                                {{ request($filter) }}
                                <a href="{{ request()->fullUrlWithQuery([$filter => null]) }}" style="text-decoration:none; color:white; margin-left:2px;">×</a>
                            </span>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <form method="GET" action="{{ route('todos.index') }}">
            <div style="display:grid; grid-template-columns: 1fr; gap:8px;">
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Enter keywords...">
            </div>

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:12px; margin-top:12px;">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                        <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>Ongoing</option>
                        <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Done</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Priority</label>
                    <select name="priority" class="form-control">
                        <option value="">All</option>
                        <option value="top" {{ request('priority')=='top'?'selected':'' }}>Top</option>
                        <option value="high" {{ request('priority')=='high'?'selected':'' }}>High</option>
                        <option value="medium" {{ request('priority')=='medium'?'selected':'' }}>Medium</option>
                        <option value="low" {{ request('priority')=='low'?'selected':'' }}>Low</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Assigned To</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">All</option>
                        @foreach(['ADMIN UNIT','CLYDE','MARGIE','MELETH','JACKIE','PATRICK','MITCH'] as $person)
                            <option value="{{ $person }}" {{ request('assigned_to')==$person?'selected':'' }}>{{ $person }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="display:flex; align-items:flex-end; gap:8px;">
                    <button type="submit" class="btn-red"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('todos.index') }}" class="btn-gray">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="table-header" style="display:flex; justify-content:flex-end;">
            <a href="{{ route('todos.create') }}" class="btn-red"><i class="fas fa-plus"></i> Add Task</a>
        </div>

        <div style="overflow-x:auto; max-width:100%;">
            <table style="min-width:900px; width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">ACTION</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">DATE ADDED</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:230px; border-bottom:2px solid #8b0000;">PRIORITY</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">ASSIGNED TO</th>
                        <th style="text-align:center; padding:12px 8px; min-width:200px; border-bottom:2px solid #8b0000;">TASK</th>
                        <th style="text-align:center; padding:12px 8px; min-width:250px; border-bottom:2px solid #8b0000;">WHAT TO DO</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">DEADLINE</th>
                        <th style="text-align:center; padding:12px 8px; min-width:200px; border-bottom:2px solid #8b0000;">REMARKS</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:230px; border-bottom:2px solid #8b0000;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todos as $index => $todo)

                    <tr id="todoRow-{{ $todo->id }}" class="clickable-row" data-href="{{ route('todos.show', $todo) }}" style="cursor: pointer; {{ ($todo->due_date && $todo->due_date < now() && $todo->status != 'completed') ? 'background:#fff5f5;' : '' }}">

                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;" onclick="event.stopPropagation();">
                            <div style="display:flex; gap:4px; align-items:center; justify-content:flex-start;">
                                <a href="{{ route('todos.edit',$todo) }}" class="btn-blue" title="Edit" style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('todos.destroy', $todo) }}" method="POST" style="display:inline;" id="deleteForm-{{ $todo->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-danger" title="Delete" onclick="confirmDelete({{ $todo->id }}, '{{ $todo->title }}')" style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>

                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;">{{ $todo->created_at?->format('n-j-Y') ?? 'No date' }}</td>

                        <!-- PRIORITY -->
                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:230px;" onclick="event.stopPropagation();">
                            <select 
                                class="form-control inline-select 
                                {{ match($todo->priority) {
                                    'top' => 'badge-top',
                                    'high' => 'badge-high',
                                    'medium' => 'badge-medium',
                                    'low' => 'badge-low',
                                    default => ''
                                } }}"
                                onchange="changePriority(this, {{ $todo->id }}, this.value)"
                                style="font-size: 11px; padding: 6px 8px; border-radius: 4px; border: 1px solid #ddd; background: white; cursor: pointer; width: 100%;">

                                <option value="top" {{ $todo->priority=='top'?'selected':'' }}>TOP</option>
                                <option value="high" {{ $todo->priority=='high'?'selected':'' }}>HIGH</option>
                                <option value="medium" {{ $todo->priority=='medium'?'selected':'' }}>MEDIUM</option>
                                <option value="low" {{ $todo->priority=='low'?'selected':'' }}>LOW</option>
                            </select>
                        </td>

                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;">{{ $todo->assigned_to ?? 'Unassigned' }}</td>

                        <td style="text-align:left; padding:20px 20px 20px 20px; min-width:200px; word-wrap:break-word; font-size: 13px; font-weight: 600;">{{ $todo->title }}</td>

                        <td style="text-align:left; padding:20px 20px 20px 20px; min-width:250px; word-wrap:break-word; font-size: 12px; color: #6c757d;">{{ $todo->description ?? 'No description' }}</td>

                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;">{{ $todo->due_date?->format('M d, Y') ?? '—' }}</td>

                        <td style="text-align:left; padding:20px 20px 20px 20px; min-width:200px; word-wrap:break-word; font-size: 12px; color: #6c757d;">{{ $todo->remarks ?? 'No remarks' }}</td>

                        <!-- STATUS -->
                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:230px;" onclick="event.stopPropagation();">
                            <select 
                                class="form-control inline-select 
                                {{ match($todo->status) {
                                    'pending' => 'status-pending',
                                    'in_progress' => 'status-ongoing',
                                    'completed' => 'status-done',
                                    'cancelled' => 'status-cancelled',
                                    default => ''
                                } }}"
                                onchange="changeStatus(this, {{ $todo->id }}, this.value)"
                            >
                                <option value="pending" {{ $todo->status=='pending'?'selected':'' }}>PENDING</option>
                                <option value="in_progress" {{ $todo->status=='in_progress'?'selected':'' }}>ON GOING</option>
                                <option value="completed" {{ $todo->status=='completed'?'selected':'' }}>DONE</option>
                                <option value="cancelled" {{ $todo->status=='cancelled'?'selected':'' }}>CANCELLED</option>
                            </select>
                        </td>

                    </tr>

                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center; padding:60px;">
                            <div style="background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%); border:2px dashed rgba(192,57,43,0.2); border-radius:16px; padding:40px;">
                                <i class="fas fa-tasks" style="font-size:48px; color:#c0392b; margin-bottom:16px;"></i>
                                <h3 style="margin-bottom:8px; color:#1a1a2e;">No Tasks Found</h3>
                                <p style="margin-bottom:20px; color:#64748b;">Start by adding your first task.</p>
                                <a href="{{ route('todos.create') }}" class="btn-red">
                                    <i class="fas fa-plus"></i> Add Task
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="padding:16px 20px; display:flex; justify-content:center; align-items:center; gap:16px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                @if($todos->onFirstPage())
                    <span style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#d1d5db; font-size:13px; font-weight:500; cursor:not-allowed;">
                        <i class="fas fa-chevron-left"></i> Previous
                    </span>
                @else
                    <a href="{{ $todos->previousPageUrl() }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                @endif
                
                <div style="display:flex; gap:4px;">
                    @for($i = 1; $i <= min(3, $todos->lastPage()); $i++)
                        @if($todos->currentPage() == $i)
                            <span style="padding:8px 12px; background:linear-gradient(135deg, #c0392b 0%, #8b0000 100%); border:none; border-radius:6px; color:#ffffff; font-size:13px; font-weight:600; cursor:pointer;">{{ $i }}</span>
                        @else
                            <a href="{{ $todos->url($i) }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">{{ $i }}</a>
                        @endif
                    @endfor
                </div>
                
                @if($todos->hasMorePages())
                    <a href="{{ $todos->nextPageUrl() }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#d1d5db; font-size:13px; font-weight:500; cursor:not-allowed;">
                        Next <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="notification-container" id="notificationContainer"></div>

    <script>
        // Clickable rows functionality
        document.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', function(e) {
                // Don't redirect if clicking on dropdowns, buttons, or their children
                if (e.target.closest('select') || e.target.closest('button') || e.target.closest('a')) {
                    return;
                }
                window.location.href = this.dataset.href;
            });
        });

        // Delete confirmation function
        function confirmDelete(id, title){
            showConfirmDialog({
                title: 'Delete Task',
                message: `Are you sure you want to delete <strong>${title}</strong>? This cannot be undone.`,
                confirmText: 'Delete',
                cancelText: 'Cancel',
                onConfirm: () => {
                    fetch(`/todos/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json().then(data => ({ ok: res.ok, data })))
                    .then(result => {
                        if (result.ok && result.data.success) {
                            const row = document.getElementById(`todoRow-${id}`);
                            if (row) {
                                row.remove();
                            }
                            showCompletedNotification('Task deleted', 'Task has been successfully deleted.');
                            return;
                        }

                        const errorMessage = result.data.message || result.data.error || 'Failed to delete the task.';
                        showErrorNotification('Delete Failed', errorMessage);
                    })
                    .catch(() => {
                        showErrorNotification('Delete Failed', 'An error occurred while deleting the task. Please try again.');
                    });
                }
            });
        }

        // Status update function
        function changeStatus(el, id, value){

            el.classList.remove(
                'status-pending',
                'status-ongoing',
                'status-done',
                'status-cancelled'
            );

            if(value === 'pending') el.classList.add('status-pending');
            if(value === 'in_progress') el.classList.add('status-ongoing');
            if(value === 'completed') el.classList.add('status-done');
            if(value === 'cancelled') el.classList.add('status-cancelled');

            fetch(`/todos/${id}/update-status`, {
                method:'PATCH',
                headers:{
                    'X-CSRF-TOKEN':'{{ csrf_token() }}',
                    'Content-Type':'application/json'
                },
                body: JSON.stringify({status:value})
            })
            .then(res=>res.json())
            .then(data=>{
                if(data.success){
                    showCompletedNotification('Status Updated', `Status updated to ${value}`);
                }
            });
        }
        
        function changePriority(el, id, value){

            // Remove all color classes
            el.classList.remove('badge-top','badge-high','badge-medium','badge-low');

            // Apply correct class
            if(value === 'top') el.classList.add('badge-top');
            if(value === 'high') el.classList.add('badge-high');
            if(value === 'medium') el.classList.add('badge-medium');
            if(value === 'low') el.classList.add('badge-low');

            // API call
            fetch(`/todos/${id}/update-priority`, {
                method:'PATCH',
                headers:{
                    'X-CSRF-TOKEN':'{{ csrf_token() }}',
                    'Content-Type':'application/json'
                },
                body: JSON.stringify({priority:value})
            })
            .then(res=>res.json())
            .then(data=>{
                if(data.success){
                    showCompletedNotification('Priority Updated', `Priority updated to ${value}`);
                }
            });
        }

        // Notification functions
        function showCompletedNotification(title, message) {
            const notification = document.createElement('div');
            notification.className = 'notification success';
            notification.innerHTML = `
                <div class="notification-header">
                    <div class="notification-title">✓ ${title}</div>
                    <button class="notification-close" onclick="removeNotification(this)">&times;</button>
                </div>
                <div class="notification-message">${message}</div>
            `;
            
            document.getElementById('notificationContainer').appendChild(notification);
            
            setTimeout(() => {
                removeNotification(notification.querySelector('.notification-close'));
            }, 3000);
        }

        function showErrorNotification(title, message) {
            const notification = document.createElement('div');
            notification.className = 'notification warning';
            notification.innerHTML = `
                <div class="notification-header">
                    <div class="notification-title">✖ ${title}</div>
                    <button class="notification-close" onclick="removeNotification(this)">&times;</button>
                </div>
                <div class="notification-message">${message}</div>
            `;
            
            document.getElementById('notificationContainer').appendChild(notification);
            
            setTimeout(() => {
                removeNotification(notification.querySelector('.notification-close'));
            }, 4000);
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

        function showConfirmDialog(options) {
            const {
                title = 'Confirm Action',
                message = 'Are you sure you want to proceed?',
                confirmText = 'Confirm',
                cancelText = 'Cancel',
                onConfirm = null,
                onCancel = null
            } = options;

            return new Promise((resolve) => {
                const notification = document.createElement('div');
                notification.className = 'notification warning';
                notification.innerHTML = `
                    <div class="notification-header">
                        <div class="notification-title">⚠ ${title}</div>
                        <button class="notification-close" onclick="removeNotification(this)">&times;</button>
                    </div>
                    <div class="notification-message">${message}</div>
                    <div class="notification-actions">
                        <button class="notification-btn notification-btn-cancel" onclick="cancelDialog()">${cancelText}</button>
                        <button class="notification-btn notification-btn-confirm" onclick="confirmDialog()">${confirmText}</button>
                    </div>
                `;
                
                document.getElementById('notificationContainer').appendChild(notification);

                window.confirmDialog = () => {
                    if (onConfirm) onConfirm();
                    removeNotification(notification.querySelector('.notification-close'));
                    resolve(true);
                    delete window.confirmDialog;
                    delete window.cancelDialog;
                };

                window.cancelDialog = () => {
                    if (onCancel) onCancel();
                    removeNotification(notification.querySelector('.notification-close'));
                    resolve(false);
                    delete window.confirmDialog;
                    delete window.cancelDialog;
                };
            });
        }
    </script>

    <style>
        .inline-select {
            width: 100%;
            cursor: pointer;
            font-weight: 600;
            background: white;
            color: #333;
        }

        /* When dropdown is OPEN → force neutral */
        .inline-select:focus {
            background: white !important;
            color: #333 !important;
        }

        /* ===== PRIORITY COLORS (ONLY WHEN CLOSED) ===== */
        .inline-select.badge-top {
            background: #dc2626 !important;
            color: #fff !important;
        }

        .inline-select.badge-high {
            background: #ef4444 !important;
            color: #fff !important;
        }

        .inline-select.badge-medium {
            background: #facc15 !important;
            color: #000 !important;
        }

        .inline-select.badge-low {
            background: #22c55e !important;
            color: #fff !important;
        }

        /* ===== STATUS COLORS (ONLY WHEN CLOSED) ===== */
        .inline-select.status-pending {
            background: #ef4444 !important; /* RED */
            color: #fff !important;
        }

        .inline-select.status-ongoing {
            background: #eab308 !important; /* YELLOW */
            color: #000 !important;
        }

        .inline-select.status-done {
            background: #16a34a !important; /* GREEN */
            color: #fff !important;
        }

        .inline-select.status-cancelled {
            background: #6b7280 !important; /* GRAY */
            color: #fff !important;
}
        /* FORCE DROPDOWN OPTIONS TO STAY CLEAN */
        .inline-select option {
            background: #ffffff !important;
            color: #000000 !important;
        }

        /* Optional: better hover contrast (browser-dependent) */
        .inline-select option:hover {
            background: #e5e7eb !important;
            color: #000 !important;
        }
        
        /* Notification System Styles */
        .notification-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            pointer-events: none;
            width: 100%;
            max-width: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 16px;
        }

        .notification {
            background: #fff;
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.18);
            border: 1px solid rgba(219, 152, 74, 0.5);
            min-width: 320px;
            max-width: 440px;
            pointer-events: all;
            animation: slideInFromTop 0.25s ease-out;
            position: relative;
            overflow: hidden;
        }

        .notification.success {
            border-color: #27ae60;
        }

        .notification.warning {
            border-color: #f39c12;
        }

        .notification.info {
            border-color: #3498db;
        }

        .notification.danger {
            border-color: #dc2626;
        }

        /* Notification entrance for centered overlay */
        @keyframes slideInFromTop {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .notification-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .notification-title {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #555;
            font-size: 14px;
            line-height: 1.4;
        }

        .notification-actions {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .notification-btn {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
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

        .notification.removing {
            animation: slideOutOpacity 0.2s ease-out forwards;
        }

        @keyframes slideOutOpacity {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-8px);
            }
        }
    </style>
</x-app-layout>