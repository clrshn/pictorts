<x-app-layout>
    <x-slot name="header">
        <h1>Office Management</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / Office Management</div>
    </x-slot>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof window.showNotification === 'function') {
                    window.showNotification({
                        type: 'success',
                        title: 'Success!',
                        message: '{{ session('success') }}',
                        duration: 3000
                    });
                }
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof window.showNotification === 'function') {
                    window.showNotification({
                        type: 'danger',
                        title: 'Error!',
                        message: '{{ session('error') }}',
                        duration: 3000
                    });
                }
            });
        </script>
    @endif

    <div class="filter-box office-filter-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; gap:16px; flex-wrap:wrap;">
            <h3 style="margin:0;">Search Filter</h3>
            @if(request('search'))
                <div class="active-filter-list">
                    <span class="active-filter-label">Active Filters:</span>
                    <span class="active-filter-pill">
                        {{ request('search') }}
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove search filter">×</a>
                    </span>
                </div>
            @endif
        </div>

        <form method="GET" action="{{ route('offices.index') }}">
            <div style="display:grid; grid-template-columns: 1fr; gap:8px;">
                <div class="form-group" style="margin:0">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by office code or office name...">
                </div>
            </div>
            <div class="form-group" style="display:flex; gap:12px; margin-top:24px; justify-content:flex-end;">
                <button type="submit" class="btn-red" style="min-width: 100px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="{{ route('offices.index') }}" class="btn-gray" style="min-width: 100px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Offices Table -->
    <div class="table-card">
        <div class="table-header" style="display: flex; justify-content: flex-end; align-items: center;">
            <a href="{{ route('offices.create') }}" class="btn-red" style="min-width: 100px; height: 36px; display: inline-flex; align-items: center; justify-content: center;"><i class="fas fa-plus"></i> Add New Office</a>
        </div>

        <div style="overflow-x:auto; max-width:100%;">
            <table style="min-width:600px; width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:80px; border-bottom:2px solid #8b0000;">SEQ #</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">OFFICE CODE</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; min-width:300px; border-bottom:2px solid #8b0000;">OFFICE NAME</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:100px; border-bottom:2px solid #8b0000;">USERS</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offices as $index => $office)
                        <tr>
                            <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:80px;">{{ $offices->firstItem() + $index }}</td>
                            <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px; font-weight:600;">{{ $office->code }}</td>
                            <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; min-width:300px;">{{ $office->name }}</td>
                            <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:100px;">
                                <span class="badge badge-primary">{{ $office->users->count() }}</span>
                            </td>
                            <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;">
                                <div style="display:flex; gap:4px; align-items:center; justify-content:flex-start;">
                                    <a href="{{ route('offices.edit', $office) }}" class="btn-blue" title="Edit Office" style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-edit"></i></a>
                                    @if($office->users->count() == 0)
                                        <form action="{{ route('offices.destroy', $office) }}" method="POST" style="display:inline;" id="deleteForm-{{ $office->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn-danger" title="Delete Office" onclick="confirmDelete({{ $office->id }}, '{{ $office->code }}')" style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:30px; color:#999;">No offices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="padding:16px 20px; display:flex; justify-content:center; align-items:center; gap:16px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                @if($offices->onFirstPage())
                    <span style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#d1d5db; font-size:13px; font-weight:500; cursor:not-allowed;">
                        <i class="fas fa-chevron-left"></i> Previous
                    </span>
                @else
                    <a href="{{ $offices->previousPageUrl() }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                @endif
                
                <div style="display:flex; gap:4px;">
                    @for($i = 1; $i <= min(3, $offices->lastPage()); $i++)
                        @if($offices->currentPage() == $i)
                            <span style="padding:8px 12px; background:linear-gradient(135deg, #c0392b 0%, #8b0000 100%); border:none; border-radius:6px; color:#ffffff; font-size:13px; font-weight:600; cursor:pointer;">{{ $i }}</span>
                        @else
                            <a href="{{ $offices->url($i) }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">{{ $i }}</a>
                        @endif
                    @endfor
                </div>
                
                @if($offices->hasMorePages())
                    <a href="{{ $offices->nextPageUrl() }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">
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

    <!-- Notification Container -->
    <div class="notification-container" id="notificationContainer"></div>

    <style>
        /* Consistent Font System */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #2c3e50;
        }

        .table-card {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
        }

        .office-filter-box {
            margin-bottom: 18px;
        }

        .table-header h3 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        table th {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }

        table td {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            color: #2c3e50;
        }

        .badge {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-red, .btn-blue, .btn-green, .btn-gray, .btn-danger {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
        }

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

        .notification.danger {
            border-left-color: #e74c3c;
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
        // Modern Notification System
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
                        iconHtml = '<i class="fas fa-check-circle"></i>';
                        break;
                    case 'danger':
                        iconHtml = '<i class="fas fa-exclamation-circle"></i>';
                        break;
                    default:
                        iconHtml = '<i class="fas fa-info-circle"></i>';
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

        function confirmDelete(officeId, officeCode) {
            showNotification({
                type: 'danger',
                title: 'Delete Office',
                message: `Are you sure you want to delete this office?<br><br><strong>Office Code:</strong> ${officeCode}<br><strong>This action cannot be undone!</strong>`,
                duration: 0,
                actions: [
                    {
                        text: 'Cancel',
                        class: 'notification-btn-cancel',
                        onclick: 'removeNotification(this.closest(".notification").querySelector(".notification-close"));'
                    },
                    {
                        text: 'Delete',
                        class: 'notification-btn-confirm',
                        onclick: `removeNotification(this.closest(".notification").querySelector(".notification-close")); document.getElementById('deleteForm-${officeId}').submit();`
                    }
                ]
            });
        }
    </script>
</x-app-layout>
