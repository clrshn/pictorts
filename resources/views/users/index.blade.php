@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <div style="background:#f8f9fa; color:#333; padding:10px 20px; font-weight:600; font-size:18px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #dee2e6;">
                    <div><i class="fas fa-users" style="color:#c0392b;"></i> User Management</div>
                    <div>
                        <a href="{{ route('users.create') }}" class="btn-red">
                            <i class="fas fa-plus"></i> Create User
                        </a>
                    </div>
                </div>
                <div style="padding:20px;">
                    <!-- Search Users -->
                    <div style="background:#f8f9fa; padding:16px; border-radius:8px; margin-bottom:20px;">
                        <form method="GET" action="{{ route('users.index') }}">
                            <div style="display:grid; grid-template-columns: 1fr auto; gap:12px; align-items:end;">
                                <div class="form-group" style="margin:0;">
                                    <label style="font-weight:600; color:#495057; margin-bottom:6px; display:block;">Search Users</label>
                                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name, email, or office...">
                                </div>
                                <div style="display:flex; gap:8px;">
                                    <button type="submit" class="btn-red" style="padding:8px 16px;">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                    <a href="{{ route('users.index') }}" class="btn-gray" style="padding:8px 16px;">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                        
                        @if(request('search'))
                            <div style="margin-top:12px; padding:8px 12px; background:#e3f2fd; border-left:4px solid #1976d2; border-radius:4px;">
                                <span style="color:#1976d2; font-size:13px; font-weight:500;">
                                    <i class="fas fa-filter"></i> Searching for: <strong>{{ request('search') }}</strong>
                                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" style="margin-left:8px; color:#666; text-decoration:none;" title="Remove search">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" style="font-size:13px;">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th>Name</th>
                                    <th>Email (Username)</th>
                                    <th>Office</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td style="font-weight:600;">{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->office?->code ?? '—' }}</td>
                                        <td>
                                            <span class="badge {{ $user->isAdmin() ? 'badge-danger' : 'badge-primary' }}" style="font-size:11px;">
                                                {{ strtoupper($user->role) }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div style="display:flex; gap:4px; align-items:center; justify-content:center;">
                                                <a href="{{ route('users.edit', $user) }}" class="btn-blue" title="Edit" style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn-orange" onclick="document.getElementById('resetPasswordModal{{ $user->id }}').style.display='flex'" title="Reset Password" style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;" id="deleteForm-{{ $user->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn-red" onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')" title="Delete" style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Reset Password Modal -->
                                    <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
                                        <div class="modal-dialog" style="max-width:450px; width:90%; margin:20px;">
                                            <div class="modal-content" style="background:#fff; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.2); overflow:hidden;">
                                                <div class="modal-header" style="background:#c0392b; color:#fff; padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
                                                    <h5 class="modal-title" style="margin:0; font-size:16px; font-weight:600; display:flex; align-items:center; gap:8px;">
                                                        <i class="fas fa-key"></i>
                                                        Reset Password for {{ $user->name }}
                                                    </h5>
                                                    <button type="button" class="btn-close" onclick="document.getElementById('resetPasswordModal{{ $user->id }}').style.display='none'" style="background:none; border:none; color:#fff; font-size:20px; cursor:pointer; padding:0; width:24px; height:24px; display:flex; align-items:center; justify-content:center; border-radius:4px; transition:background 0.2s;">&times;</button>
                                                </div>
                                                <form action="{{ route('users.reset-password', $user) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body" style="padding:24px;">
                                                        <div class="alert alert-info" style="background:#e3f2fd; color:#1565c0; border:1px solid #bbdefb; padding:12px 16px; border-radius:6px; margin-bottom:20px; font-size:13px; line-height:1.4;">
                                                            <div style="display:flex; align-items:flex-start; gap:8px;">
                                                                <i class="fas fa-info-circle" style="margin-top:2px;"></i>
                                                                <div>
                                                                    <strong>Important:</strong> After resetting, provide the new password to the user. The user will need to use this new password for their next login.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group" style="margin-bottom:16px;">
                                                            <label style="display:block; margin-bottom:6px; color:#333; font-size:14px; font-weight:500;">
                                                                New Password <span style="color:#dc3545;">*</span>
                                                            </label>
                                                            <div style="position:relative;">
                                                                <input type="password" name="password" class="form-control" required placeholder="Enter new password" style="width:100%; padding:12px 16px 12px 40px; border:1px solid #ddd; border-radius:6px; font-size:14px; outline:none; background:#fff; color:#444; transition:border-color 0.2s, box-shadow 0.2s;">
                                                                <i class="fas fa-lock" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#999; font-size:14px;"></i>
                                                            </div>
                                                        </div>
                                                        <div class="form-group" style="margin-bottom:0;">
                                                            <label style="display:block; margin-bottom:6px; color:#333; font-size:14px; font-weight:500;">
                                                                Confirm Password <span style="color:#dc3545;">*</span>
                                                            </label>
                                                            <div style="position:relative;">
                                                                <input type="password" name="password_confirmation" class="form-control" required placeholder="Confirm new password" style="width:100%; padding:12px 16px 12px 40px; border:1px solid #ddd; border-radius:6px; font-size:14px; outline:none; background:#fff; color:#444; transition:border-color 0.2s, box-shadow 0.2s;">
                                                                <i class="fas fa-lock" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#999; font-size:14px;"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer" style="background:#f8f9fa; padding:16px 20px; display:flex; justify-content:flex-end; gap:8px; border-top:1px solid #e9ecef;">
                                                        <button type="button" class="btn-gray" onclick="document.getElementById('resetPasswordModal{{ $user->id }}').style.display='none'" style="padding:10px 20px; border:1px solid #6c757d; background:#6c757d; color:#fff; border-radius:6px; font-size:14px; font-weight:500; cursor:pointer; transition:background 0.2s;">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </button>
                                                        <button type="submit" class="btn-red" style="padding:10px 20px; border:1px solid #c0392b; background:#c0392b; color:#fff; border-radius:6px; font-size:14px; font-weight:500; cursor:pointer; transition:background 0.2s;">
                                                            <i class="fas fa-key"></i> Reset Password
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div style="padding:16px 20px; display:flex; justify-content:center; align-items:center; gap:16px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            @if($users->onFirstPage())
                                <span style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#d1d5db; font-size:13px; font-weight:500; cursor:not-allowed;">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </span>
                            @else
                                <a href="{{ $users->previousPageUrl() }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            @endif
                            
                            <div style="display:flex; gap:4px;">
                                @for($i = 1; $i <= min(3, $users->lastPage()); $i++)
                                    @if($users->currentPage() == $i)
                                        <span style="padding:8px 12px; background:linear-gradient(135deg, #c0392b 0%, #8b0000 100%); border:none; border-radius:6px; color:#ffffff; font-size:13px; font-weight:600; cursor:pointer;">{{ $i }}</span>
                                    @else
                                        <a href="{{ $users->url($i) }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">{{ $i }}</a>
                                    @endif
                                @endfor
                            </div>
                            
                            @if($users->hasMorePages())
                                <a href="{{ $users->nextPageUrl() }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">
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
            </div>
        </div>
    </div>
</div>

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

    function confirmDelete(userId, userName) {
        console.log('confirmDelete called with:', userId, userName); // Debug log
        
        showConfirmDialog({
            title: 'Delete User',
            message: `Are you sure you want to delete this user?<br><br><strong>User:</strong> ${userName}<br><strong>This action cannot be undone!</strong>`,
            confirmText: 'Delete',
            cancelText: 'Cancel',
            confirmClass: 'notification-btn-confirm',
            onConfirm: function() {
                console.log('Delete confirmed, submitting form:', userId); // Debug log
                const form = document.getElementById(`deleteForm-${userId}`);
                if (form) {
                    form.submit();
                } else {
                    console.error('Form not found:', `deleteForm-${userId}`);
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
        console.log('Users page loaded, notification system ready');
        // Uncomment to test automatically:
        // setTimeout(testNotification, 1000);
    });
</script>
@endsection
