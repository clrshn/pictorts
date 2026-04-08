<!-- Session-based Notifications -->
@if(session('success'))
    <div class="notification-container session-notification">
        <div class="notification success">
            <div class="notification-header">
                <div class="notification-title">Success</div>
                <button class="notification-close" onclick="removeNotification(this)">&times;</button>
            </div>
            <div class="notification-message">{{ session('success') }}</div>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="notification-container session-notification">
        <div class="notification danger">
            <div class="notification-header">
                <div class="notification-title">Error</div>
                <button class="notification-close" onclick="removeNotification(this)">&times;</button>
            </div>
            <div class="notification-message">{{ session('error') }}</div>
        </div>
    </div>
@endif

@if(session('warning'))
    <div class="notification-container session-notification">
        <div class="notification warning">
            <div class="notification-header">
                <div class="notification-title">Warning</div>
                <button class="notification-close" onclick="removeNotification(this)">&times;</button>
            </div>
            <div class="notification-message">{{ session('warning') }}</div>
        </div>
    </div>
@endif

@if(session('info'))
    <div class="notification-container session-notification">
        <div class="notification info">
            <div class="notification-header">
                <div class="notification-title">Info</div>
                <button class="notification-close" onclick="removeNotification(this)">&times;</button>
            </div>
            <div class="notification-message">{{ session('info') }}</div>
        </div>
    </div>
@endif

@if(session('updated'))
    <div class="notification-container session-notification">
        <div class="notification success">
            <div class="notification-header">
                <div class="notification-title">Updated</div>
                <button class="notification-close" onclick="removeNotification(this)">&times;</button>
            </div>
            <div class="notification-message">{{ session('updated') }}</div>
        </div>
    </div>
@endif

@if(session('deleted'))
    <div class="notification-container session-notification">
        <div class="notification success">
            <div class="notification-header">
                <div class="notification-title">Deleted</div>
                <button class="notification-close" onclick="removeNotification(this)">&times;</button>
            </div>
            <div class="notification-message">{{ session('deleted') }}</div>
        </div>
    </div>
@endif

<!-- JavaScript Notification Container -->
<div class="notification-container" id="notificationContainer"></div>

<style>
/* Session Notification Container */
.session-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    max-width: 400px;
}

/* Notification System Styles */
.notification-container {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
    pointer-events: none;
    padding: 0 16px;
}

.notification {
    background: #fff;
    border-radius: 10px;
    padding: 16px 18px;
    margin-bottom: 12px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border-left: 4px solid #ddd;
    pointer-events: all;
    overflow: hidden;
    animation: slideInFromTop 0.3s ease-out;
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

.notification.danger {
    border-left-color: #dc2626;
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

<script>
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

// Auto-remove session notifications after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const sessionNotifications = document.querySelectorAll('.session-notification .notification');
    sessionNotifications.forEach(notification => {
        setTimeout(() => {
            const closeButton = notification.querySelector('.notification-close');
            if (closeButton) {
                removeNotification(closeButton);
            }
        }, 5000);
    });
});
</script>
