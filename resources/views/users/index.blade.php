@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <div style="background:#333; color:#fff; padding:10px 20px; font-weight:600; font-size:13px; display:flex; justify-content:space-between; align-items:center;">
                    <div><i class="fas fa-users"></i> User Management</div>
                    <div>
                        <a href="{{ route('users.create') }}" class="btn-red">
                            <i class="fas fa-plus"></i> Create User
                        </a>
                    </div>
                </div>
                <div style="padding:20px;">
                    @if(session('success'))
                        <div class="alert alert-success" style="margin-bottom:20px;">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger" style="margin-bottom:20px;">
                            {{ session('error') }}
                        </div>
                    @endif

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
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reset Password for {{ $user->name }}</h5>
                                                    <button type="button" class="btn-close" onclick="document.getElementById('resetPasswordModal{{ $user->id }}').style.display='none'">&times;</button>
                                                </div>
                                                <form action="{{ route('users.reset-password', $user) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="alert alert-info">
                                                            <strong>Note:</strong> After resetting, provide the new password to the user.
                                                        </div>
                                                        <div class="form-group">
                                                            <label>New Password <span class="text-danger">*</span></label>
                                                            <input type="password" name="password" class="form-control" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Confirm Password <span class="text-danger">*</span></label>
                                                            <input type="password" name="password_confirmation" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn-gray" onclick="document.getElementById('resetPasswordModal{{ $user->id }}').style.display='none'">Cancel</button>
                                                        <button type="submit" class="btn-red">Reset Password</button>
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

                    <div style="margin-top:20px;">
                        {{ $users->links('pagination::bootstrap-4') }}
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
