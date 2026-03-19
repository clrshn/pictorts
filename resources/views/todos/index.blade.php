<x-app-layout>
    <x-slot name="header">
        <h1>Task Monitoring</h1>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Home</a> / Task Monitoring
        </div>
    </x-slot>

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
                        <th style="text-align:left; padding:15px; font-size: 12px; font-weight: 600; color: #333; border-bottom: 2px solid #8b0000; background: #f8f9fa;">DATE ADDED</th>
                        <th style="text-align:left; padding:15px; font-size: 12px; font-weight: 600; color: #333; border-bottom: 2px solid #8b0000; background: #f8f9fa;">PRIORITY</th>
                        <th style="text-align:left; padding:15px; font-size: 12px; font-weight: 600; color: #333; border-bottom: 2px solid #8b0000; background: #f8f9fa;">ASSIGNED TO</th>
                        <th style="text-align:left; padding:15px; font-size: 12px; font-weight: 600; color: #333; border-bottom: 2px solid #8b0000; background: #f8f9fa;">TASK</th>
                        <th style="text-align:left; padding:15px; font-size: 12px; font-weight: 600; color: #333; border-bottom: 2px solid #8b0000; background: #f8f9fa;">WHAT TO DO</th>
                        <th style="text-align:left; padding:15px; font-size: 12px; font-weight: 600; color: #333; border-bottom: 2px solid #8b0000; background: #f8f9fa;">DEADLINE</th>
                        <th style="text-align:left; padding:15px; font-size: 12px; font-weight: 600; color: #333; border-bottom: 2px solid #8b0000; background: #f8f9fa;">REMARKS</th>
                        <th style="text-align:left; padding:15px; font-size: 12px; font-weight: 600; color: #333; border-bottom: 2px solid #8b0000; background: #f8f9fa;">STATUS</th>
                        <th style="text-align:left; padding:15px; font-size: 12px; font-weight: 600; color: #333; border-bottom: 2px solid #8b0000; background: #f8f9fa;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todos as $index=>$todo)
                        @php
                            $isOverdue = $todo->due_date && $todo->due_date < now() && $todo->status != 'completed';
                            $statusClass = match($todo->status){'pending'=>'badge-ongoing','in_progress'=>'badge-delivered','completed'=>'badge-completed',default=>''};
                            $priorityClass = match($todo->priority){'top'=>'badge-danger','high'=>'badge-warning','medium'=>'badge-info','low'=>'badge-gray',default=>''};
                        @endphp
                        <tr class="clickable-row" data-id="{{ $todo->id }}" style="{{ $isOverdue?'background:#fff5f5;':'' }}" data-href="{{ route('todos.edit', $todo) }}">
                            <td style="padding:12px 15px; border-bottom: 1px solid #e9ecef; font-size: 12px; color: #495057; cursor: pointer;" onclick="window.location='{{ route('todos.edit', $todo) }}'">{{ $todo->created_at?->format('M d, Y') ?? 'No date' }}</td>
                            <td style="padding:12px 15px; border-bottom: 1px solid #e9ecef;" onclick="event.stopPropagation();">
                                <select class="form-control inline-select {{ $priorityClass }}" onchange="changePriority({{ $todo->id }}, this.value)" style="font-size: 11px; padding: 6px 8px; border-radius: 4px; border: 1px solid #ddd; background: white; cursor: pointer; width: 100%;">
                                    <option value="">Select Priority</option>
                                    <option value="top" {{ $todo->priority=='top'?'selected':'' }}>TOP</option>
                                    <option value="high" {{ $todo->priority=='high'?'selected':'' }}>HIGH</option>
                                    <option value="medium" {{ $todo->priority=='medium'?'selected':'' }}>MEDIUM</option>
                                    <option value="low" {{ $todo->priority=='low'?'selected':'' }}>LOW</option>
                                </select>
                            </td>
                            <td style="padding:12px 15px; border-bottom: 1px solid #e9ecef; font-size: 12px; color: #495057; cursor: pointer;" onclick="window.location='{{ route('todos.edit', $todo) }}'">{{ $todo->assigned_to ?? 'Unassigned' }}</td>
                            <td style="padding:12px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #212529; font-weight: 600; cursor: pointer;" onclick="window.location='{{ route('todos.edit', $todo) }}'">{{ $todo->title }}</td>
                            <td style="padding:12px 15px; border-bottom: 1px solid #e9ecef; font-size: 12px; color: #6c757d; cursor: pointer;" onclick="window.location='{{ route('todos.edit', $todo) }}'">{{ $todo->description ?? 'No description' }}</td>
                            <td style="padding:12px 15px; border-bottom: 1px solid #e9ecef; font-size: 12px; color: #495057; cursor: pointer;" onclick="window.location='{{ route('todos.edit', $todo) }}'">{{ $todo->due_date?->format('M d, Y') ?? '—' }}</td>
                            <td style="padding:12px 15px; border-bottom: 1px solid #e9ecef; font-size: 12px; color: #6c757d; cursor: pointer;" onclick="window.location='{{ route('todos.edit', $todo) }}'">{{ $todo->remarks ?? 'No remarks' }}</td>
                            <td style="padding:12px 15px; border-bottom: 1px solid #e9ecef;" onclick="event.stopPropagation();">
                                <select class="form-control inline-select {{ $statusClass }}" onchange="changeStatus({{ $todo->id }}, this.value)" style="font-size: 11px; padding: 6px 8px; border-radius: 4px; border: 1px solid #ddd; background: white; cursor: pointer; width: 100%;">
                                    <option value="pending" {{ $todo->status=='pending'?'selected':'' }}>PENDING</option>
                                    <option value="in_progress" {{ $todo->status=='in_progress'?'selected':'' }}>ON GOING</option>
                                    <option value="completed" {{ $todo->status=='completed'?'selected':'' }}>DONE</option>
                                </select>
                            </td>
                            <td style="padding:12px 15px; border-bottom: 1px solid #e9ecef;" onclick="event.stopPropagation();">
                                <div style="display: flex; gap: 6px; align-items: center;">
                                    <a href="{{ route('todos.edit',$todo) }}" class="btn-blue" style="padding: 4px 8px; font-size: 11px; text-decoration: none; border-radius: 4px; background: #007bff; color: white; border: none; cursor: pointer;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn-danger" onclick="confirmDelete({{ $todo->id }}, '{{ $todo->title }}')" style="padding: 4px 8px; font-size: 11px; border-radius: 4px; background: #dc3545; color: white; border: none; cursor: pointer;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding:60px;">
                                <div style="background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%); border:2px dashed rgba(192,57,43,0.2); border-radius:16px; padding:40px;">
                                    <i class="fas fa-tasks" style="font-size:48px; color:#c0392b; margin-bottom:16px;"></i>
                                    <h3 style="margin-bottom:8px; color:#1a1a2e;">No Tasks Found</h3>
                                    <p style="margin-bottom:20px; color:#64748b;">Start by adding your first task to the system.</p>
                                    <a href="{{ route('todos.create') }}" class="btn-red"><i class="fas fa-plus"></i> Add Task</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding:16px 20px; display:flex; justify-content:center; gap:12px;">
            {{ $todos->links() }}
        </div>
    </div>

    <div class="notification-container" id="notificationContainer"></div>

    <script>
        // Delete confirmation function
        function confirmDelete(id,title){
            showConfirmDialog({
                title:'Delete Task',
                message:`Are you sure you want to delete <strong>${title}</strong>? This cannot be undone.`,
                confirmText:'Delete',
                cancelText:'Cancel',
                onConfirm:()=>{ 
                    fetch(`/todos/${id}`, {method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
                    .then(res=>res.ok ? window.location.reload() : alert('Failed to delete')); 
                }
            });
        }

        // Status update function
        function changeStatus(id,value){
            fetch(`/todos/${id}/update-status`, {
                method:'PATCH',
                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
                body: JSON.stringify({status:value})
            }).then(res=>res.json()).then(data=>{
                if(data.success) showCompletedNotification('Status Updated', `Status updated to ${value}`);
            });
        }

        // Priority update function
        function changePriority(id,value){
            fetch(`/todos/${id}/update-priority`, {
                method:'PATCH',
                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
                body: JSON.stringify({priority:value})
            }).then(res=>res.json()).then(data=>{
                if(data.success) showCompletedNotification('Priority Updated', `Priority updated to ${value}`);
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
        .inline-select{width:100%; cursor:pointer; font-weight:600;}
        .badge-ongoing{background:#facc15;color:white;}
        .badge-delivered{background:#3b82f6;color:white;}
        .badge-completed{background:#10b981;color:white;}
        .badge-danger{background:#ef4444;color:white;}
        .badge-warning{background:#f59e0b;color:white;}
        .badge-info{background:#3b82f6;color:white;}
        .badge-gray{background:#9ca3af;color:white;}
        
        /* Dropdown color styles */
        .form-control.inline-select.badge-danger {
            background: #dc2626 !important;
            color: white !important;
            border-color: #991b1b !important;
        }
        
        .form-control.inline-select.badge-warning {
            background: #f59e0b !important;
            color: white !important;
            border-color: #d97706 !important;
        }
        
        .form-control.inline-select.badge-info {
            background: #3b82f6 !important;
            color: white !important;
            border-color: #1e40af !important;
        }
        
        .form-control.inline-select.badge-gray {
            background: #6b7280 !important;
            color: white !important;
            border-color: #374151 !important;
        }
        
        .form-control.inline-select.badge-ongoing {
            background: #eab308 !important;
            color: white !important;
            border-color: #a16207 !important;
        }
        
        .form-control.inline-select.badge-delivered {
            background: #3b82f6 !important;
            color: white !important;
            border-color: #1e40af !important;
        }
        
        .form-control.inline-select.badge-completed {
            background: #16a34a !important;
            color: white !important;
            border-color: #15803d !important;
        }
        
        /* Notification System Styles */
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
            animation: slideOutRight 0.3s ease-out forwards;
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
    </style>
</x-app-layout>