<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size:38px;font-weight:800;color:#1a1a2e;">Notifications</h2>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Home</a> / Notification Center
        </div>
    </x-slot>

    <div class="table-card" style="margin-bottom: 20px;">
        <div style="padding: 22px 24px 16px; border-bottom: 1px solid rgba(226,232,240,0.86);">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                <div>
                    <h3 style="margin:0; font-size:20px; font-weight:700; color:#0f172a;">Activity Center</h3>
                    <p style="margin:6px 0 0; color:#64748b; font-size:14px;">Track updates, forwards, and reminders in one place.</p>
                </div>
                <button type="button" class="btn-gray" onclick="markAllNotificationsReadFromPage()">
                    <i class="fa-solid fa-check-double"></i> Mark All Read
                </button>
            </div>
        </div>

        <div style="padding: 18px 24px 10px;">
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button type="button" class="notif-filter-chip active" data-category="all" onclick="setNotificationFilter('all')">All</button>
                <button type="button" class="notif-filter-chip" data-category="todo" onclick="setNotificationFilter('todo')">To-Do</button>
                <button type="button" class="notif-filter-chip" data-category="document" onclick="setNotificationFilter('document')">Documents</button>
                <button type="button" class="notif-filter-chip" data-category="financial" onclick="setNotificationFilter('financial')">Financial</button>
                <button type="button" class="notif-filter-chip" data-category="reminder" onclick="setNotificationFilter('reminder')">Reminders</button>
                <button type="button" class="notif-filter-chip" id="unreadOnlyChip" onclick="toggleUnreadOnly()">Unread Only</button>
            </div>
        </div>

        <div style="padding: 10px 24px 24px;">
            <div id="notificationsPageFeed">
                <div class="notification-feed-empty">Loading notifications...</div>
            </div>
        </div>
    </div>

    <style>
        .notif-filter-chip {
            border: 1px solid rgba(203,213,225,0.9);
            background: linear-gradient(135deg, rgba(255,255,255,0.96) 0%, rgba(248,250,252,0.92) 100%);
            color: #475569;
            padding: 9px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .notif-filter-chip:hover,
        .notif-filter-chip.active {
            color: #c0392b;
            border-color: rgba(248,113,113,0.38);
            background: linear-gradient(90deg, rgba(37,99,235,0.08) 0%, rgba(255,255,255,0.98) 48%, rgba(220,38,38,0.08) 100%);
            box-shadow: 0 10px 18px rgba(15,23,42,0.07);
        }
        .notification-page-list {
            display: grid;
            gap: 12px;
        }
        .notification-page-item {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            text-decoration: none;
            border-radius: 18px;
            border: 1px solid rgba(226,232,240,0.9);
            background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.94) 100%);
            padding: 16px;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }
        .notification-page-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 26px rgba(15,23,42,0.08);
            border-color: rgba(148,163,184,0.34);
        }
        .notification-page-item.is-unread {
            background: linear-gradient(90deg, rgba(37,99,235,0.06) 0%, rgba(255,255,255,0.98) 46%, rgba(220,38,38,0.08) 100%);
        }
        .notification-page-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: linear-gradient(135deg, rgba(37,99,235,0.12) 0%, rgba(220,38,38,0.12) 100%);
            color: #1e3a8a;
        }
        .notification-page-item[data-type="success"] .notification-page-icon {
            background: linear-gradient(135deg, rgba(34,197,94,0.16) 0%, rgba(21,128,61,0.12) 100%);
            color: #166534;
        }
        .notification-page-item[data-type="danger"] .notification-page-icon {
            background: linear-gradient(135deg, rgba(248,113,113,0.18) 0%, rgba(153,27,27,0.12) 100%);
            color: #991b1b;
        }
        .notification-page-item[data-type="warning"] .notification-page-icon {
            background: linear-gradient(135deg, rgba(251,191,36,0.18) 0%, rgba(217,119,6,0.14) 100%);
            color: #92400e;
        }
        .notification-page-content {
            min-width: 0;
            flex: 1;
        }
        .notification-page-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 6px;
            align-items: center;
            flex-wrap: wrap;
        }
        .notification-page-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }
        .notification-page-time {
            font-size: 12px;
            color: #64748b;
        }
        .notification-page-message {
            color: #475569;
            font-size: 13px;
            line-height: 1.55;
        }
        .notification-page-badges {
            display:flex;
            gap:8px;
            flex-wrap:wrap;
            margin-top: 8px;
        }
        .notification-page-pill {
            display:inline-flex;
            align-items:center;
            padding:4px 9px;
            border-radius:999px;
            background: rgba(148,163,184,0.14);
            color: #475569;
            font-size: 11px;
            font-weight:700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>

    <script>
        const notificationPageState = {
            category: 'all',
            unreadOnly: false,
        };

        async function loadNotificationsPage() {
            const container = document.getElementById('notificationsPageFeed');
            const params = new URLSearchParams({
                limit: '100',
                category: notificationPageState.category,
            });

            if (notificationPageState.unreadOnly) {
                params.set('unread', '1');
            }

            container.innerHTML = '<div class="notification-feed-empty">Loading notifications...</div>';

            try {
                const response = await fetch(`{{ route('notifications.feed') }}?${params.toString()}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load notifications.');
                }

                const data = await response.json();
                renderNotificationsPage(data.items || []);
            } catch (error) {
                container.innerHTML = '<div class="notification-feed-empty">Unable to load notifications right now.</div>';
            }
        }

        function renderNotificationsPage(items) {
            const container = document.getElementById('notificationsPageFeed');

            if (!items.length) {
                container.innerHTML = '<div class="notification-feed-empty">No notifications match your current filter.</div>';
                return;
            }

            container.innerHTML = `
                <div class="notification-page-list">
                    ${items.map((item) => {
                        const unreadClass = item.read_at ? '' : ' is-unread';
                        const url = item.url || '#';
                        return `
                            <a href="${url}" class="notification-page-item${unreadClass}" data-id="${escapeHtml(item.id)}" data-synthetic="${item.synthetic ? 'true' : 'false'}" data-type="${escapeHtml(item.type || 'info')}" onclick="handleNotificationsPageClick(event, this)">
                                <span class="notification-page-icon"><i class="${escapeHtml(item.icon || 'fa-solid fa-bell')}"></i></span>
                                <span class="notification-page-content">
                                    <span class="notification-page-top">
                                        <span class="notification-page-title">${escapeHtml(item.title || 'Notification')}</span>
                                        <span class="notification-page-time">${escapeHtml(item.time_label || '')}</span>
                                    </span>
                                    <div class="notification-page-message">${escapeHtml(item.message || '')}</div>
                                    <div class="notification-page-badges">
                                        <span class="notification-page-pill">${escapeHtml(item.category || 'general')}</span>
                                        ${item.read_at ? '' : '<span class="notification-page-pill">Unread</span>'}
                                    </div>
                                </span>
                            </a>
                        `;
                    }).join('')}
                </div>
            `;
        }

        function setNotificationFilter(category) {
            notificationPageState.category = category;
            document.querySelectorAll('.notif-filter-chip[data-category]').forEach((chip) => {
                chip.classList.toggle('active', chip.dataset.category === category);
            });
            loadNotificationsPage();
        }

        function toggleUnreadOnly() {
            notificationPageState.unreadOnly = !notificationPageState.unreadOnly;
            document.getElementById('unreadOnlyChip').classList.toggle('active', notificationPageState.unreadOnly);
            loadNotificationsPage();
        }

        async function handleNotificationsPageClick(event, element) {
            const id = element.dataset.id;
            const synthetic = element.dataset.synthetic === 'true';

            if (!synthetic && id) {
                try {
                    await fetch(`{{ url('/notifications') }}/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                } catch (error) {
                }
            }
        }

        async function markAllNotificationsReadFromPage() {
            try {
                await fetch('{{ route('notifications.read-all') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (typeof loadNotificationFeed === 'function') {
                    loadNotificationFeed();
                }

                loadNotificationsPage();
            } catch (error) {
                if (typeof window.showNotification === 'function') {
                    window.showNotification({
                        type: 'danger',
                        title: 'Notification Error',
                        message: 'Unable to mark notifications as read right now.',
                        duration: 3000
                    });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', loadNotificationsPage);
    </script>
</x-app-layout>
