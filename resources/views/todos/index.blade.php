<x-app-layout>
    <x-slot name="header">
        <h1>Task Monitoring</h1>
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Home</a> / Task Monitoring
        </div>
    </x-slot>


    @if(array_sum($dueReminderData['counts'] ?? []) > 0)
        <div style="margin-bottom:16px; padding:18px; border-radius:18px; background:linear-gradient(135deg,#fff7ed 0%,#ffffff 48%,#eff6ff 100%); border:1px solid rgba(251,146,60,0.22); box-shadow:0 12px 26px rgba(15,23,42,0.06);">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:14px;">
                <div>
                    <div style="font-size:12px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#9a3412;">Task Alerts</div>
                    <h3 style="margin:4px 0 0; font-size:22px; color:#1e293b;">Upcoming Due Date Reminders</h3>
                </div>
                <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                    <a href="{{ route('todos.index', array_merge(request()->except('page'), ['due_alert' => 'overdue'])) }}" style="text-decoration:none;">
                    <div style="min-width:118px; padding:12px 14px; border-radius:14px; background:{{ ($activeDueAlert ?? null) === 'overdue' ? '#ffe4e6' : '#fff1f2' }}; border:1px solid {{ ($activeDueAlert ?? null) === 'overdue' ? 'rgba(190,24,93,0.36)' : 'rgba(244,63,94,0.18)' }}; box-shadow:{{ ($activeDueAlert ?? null) === 'overdue' ? '0 0 0 2px rgba(190,24,93,0.10)' : 'none' }};">
                        <div style="font-size:12px; color:#9f1239; font-weight:700; text-transform:uppercase;">Overdue</div>
                        <div style="font-size:28px; font-weight:800; color:#881337; line-height:1.1;">{{ $dueReminderData['counts']['overdue'] ?? 0 }}</div>
                    </div>
                    </a>
                    <a href="{{ route('todos.index', array_merge(request()->except('page'), ['due_alert' => 'today'])) }}" style="text-decoration:none;">
                    <div style="min-width:118px; padding:12px 14px; border-radius:14px; background:{{ ($activeDueAlert ?? null) === 'today' ? '#fef3c7' : '#fffbeb' }}; border:1px solid {{ ($activeDueAlert ?? null) === 'today' ? 'rgba(180,83,9,0.34)' : 'rgba(245,158,11,0.2)' }}; box-shadow:{{ ($activeDueAlert ?? null) === 'today' ? '0 0 0 2px rgba(180,83,9,0.10)' : 'none' }};">
                        <div style="font-size:12px; color:#92400e; font-weight:700; text-transform:uppercase;">Today</div>
                        <div style="font-size:28px; font-weight:800; color:#92400e; line-height:1.1;">{{ $dueReminderData['counts']['today'] ?? 0 }}</div>
                    </div>
                    </a>
                    <a href="{{ route('todos.index', array_merge(request()->except('page'), ['due_alert' => 'tomorrow'])) }}" style="text-decoration:none;">
                    <div style="min-width:118px; padding:12px 14px; border-radius:14px; background:{{ ($activeDueAlert ?? null) === 'tomorrow' ? '#dbeafe' : '#eff6ff' }}; border:1px solid {{ ($activeDueAlert ?? null) === 'tomorrow' ? 'rgba(29,78,216,0.34)' : 'rgba(59,130,246,0.18)' }}; box-shadow:{{ ($activeDueAlert ?? null) === 'tomorrow' ? '0 0 0 2px rgba(29,78,216,0.10)' : 'none' }};">
                        <div style="font-size:12px; color:#1d4ed8; font-weight:700; text-transform:uppercase;">Tomorrow</div>
                        <div style="font-size:28px; font-weight:800; color:#1d4ed8; line-height:1.1;">{{ $dueReminderData['counts']['tomorrow'] ?? 0 }}</div>
                    </div>
                    </a>
                    <a href="{{ route('todos.index', array_merge(request()->except('page'), ['due_alert' => 'soon'])) }}" style="text-decoration:none;">
                    <div style="min-width:118px; padding:12px 14px; border-radius:14px; background:{{ ($activeDueAlert ?? null) === 'soon' ? '#dcfce7' : '#f0fdf4' }}; border:1px solid {{ ($activeDueAlert ?? null) === 'soon' ? 'rgba(22,101,52,0.34)' : 'rgba(34,197,94,0.18)' }}; box-shadow:{{ ($activeDueAlert ?? null) === 'soon' ? '0 0 0 2px rgba(22,101,52,0.10)' : 'none' }};">
                        <div style="font-size:12px; color:#166534; font-weight:700; text-transform:uppercase;">Next 7 Days</div>
                        <div style="font-size:28px; font-weight:800; color:#166534; line-height:1.1;">{{ $dueReminderData['counts']['soon'] ?? 0 }}</div>
                    </div>
                    </a>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; margin-top:10px; margin-bottom:10px;">
                <button type="button" onclick="toggleTodoReminderList()" class="btn-gray" style="height:30px; min-width:92px; font-size:12px; padding:0 10px;">
                    <i class="fas fa-eye" style="font-size:11px;"></i> <span id="todoReminderToggleLabel">{{ !empty($activeDueAlert) && !empty($dueReminderData['items']) && $dueReminderData['items']->count() ? 'Hide List' : 'View List' }}</span>
                </button>
            </div>

            <div id="todoReminderList" style="display:{{ !empty($activeDueAlert) && !empty($dueReminderData['items']) && $dueReminderData['items']->count() ? 'grid' : 'none' }}; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:12px;">
                @forelse($dueReminderData['items'] as $reminder)
                    @php
                        $todoReminder = $reminder['todo'];
                        $accent = match($reminder['level']) {
                            'overdue' => ['bg' => '#fff1f2', 'border' => 'rgba(244,63,94,0.22)', 'text' => '#be123c'],
                            'today' => ['bg' => '#fffbeb', 'border' => 'rgba(245,158,11,0.22)', 'text' => '#b45309'],
                            'tomorrow' => ['bg' => '#eff6ff', 'border' => 'rgba(59,130,246,0.22)', 'text' => '#1d4ed8'],
                            default => ['bg' => '#f8fafc', 'border' => 'rgba(148,163,184,0.24)', 'text' => '#334155'],
                        };
                    @endphp
                    <a href="{{ route('todos.show', $todoReminder) }}" style="text-decoration:none; color:inherit;">
                        <div style="height:100%; padding:14px 15px; border-radius:16px; background:{{ $accent['bg'] }}; border:1px solid {{ $accent['border'] }}; box-shadow:0 8px 18px rgba(15,23,42,0.04);">
                            <div style="display:flex; justify-content:space-between; gap:10px; align-items:flex-start;">
                                <div style="font-size:12px; font-weight:800; letter-spacing:0.04em; color:{{ $accent['text'] }}; text-transform:uppercase;">{{ $reminder['label'] }}</div>
                                <div style="font-size:12px; color:#64748b; white-space:nowrap;">{{ optional($todoReminder->due_date)->format('M d, Y') }}</div>
                            </div>
                            <div style="margin-top:8px; font-size:15px; font-weight:700; color:#1e293b; line-height:1.35;">{{ $todoReminder->title }}</div>
                            <div style="margin-top:8px; display:flex; justify-content:space-between; gap:12px; align-items:center;">
                                <div style="font-size:12px; color:#64748b;">
                                    {{ $todoReminder->assigned_to ?: 'Unassigned' }}
                                </div>
                                <div style="font-size:12px; font-weight:700; color:#475569;">
                                    {{ strtoupper($todoReminder->priority) }}
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="grid-column:1 / -1; padding:14px 16px; border-radius:14px; background:#ffffff; border:1px dashed rgba(148,163,184,0.35); color:#64748b; font-size:13px;">
                        Select an alert tab to preview matching tasks here. The full matching records are shown in the table below.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <div class="filter-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="margin:0;">Search Filter</h3>

            @if(request()->hasAny(['status','priority','assigned_to','search','sort_by','due_alert']))
                <div class="active-filter-list">
                    <span class="active-filter-label">Active Filters:</span>
                    @foreach(['status','priority','assigned_to','search','sort_by','due_alert'] as $filter)
                        @if(request($filter))
                            <span class="active-filter-pill">
                                @if($filter == 'sort_by')
                                    {{ request('sort_by') == 'newest' ? 'NEWEST TO OLDEST' : (request('sort_by') == 'oldest' ? 'OLDEST TO NEWEST' : (request('sort_by') == 'az' ? 'A-Z' : (request('sort_by') == 'za' ? 'Z-A' : request('sort_by')))) }}
                                @elseif($filter == 'due_alert')
                                    {{ match(request('due_alert')) {
                                        'overdue' => 'OVERDUE',
                                        'today' => 'DUE TODAY',
                                        'tomorrow' => 'DUE TOMORROW',
                                        'soon' => 'NEXT 7 DAYS',
                                        default => strtoupper(str_replace('_', ' ', request('due_alert')))
                                    } }}
                                @elseif($filter == 'status')
                                    {{ match(request('status')) {
                                        'pending' => 'PENDING',
                                        'on-going' => 'ON-GOING',
                                        'done' => 'DONE',
                                        'cancelled' => 'CANCELLED',
                                        'in_progress' => 'ON-GOING',
                                        'completed' => 'DONE',
                                        default => strtoupper(str_replace('_', ' ', request('status')))
                                    } }}
                                @else
                                    {{ request($filter) }}
                                @endif
                                <a href="{{ request()->fullUrlWithQuery([$filter => null]) }}" style="text-decoration:none; color:white; margin-left:2px;">×</a>
                            </span>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <form method="GET" action="{{ route('todos.index') }}">
            @foreach(['status', 'priority', 'assigned_to', 'due_alert'] as $field)
                @if(request($field))
                    <input type="hidden" name="{{ $field }}" value="{{ request($field) }}">
                @endif
            @endforeach
            <div style="display:grid; grid-template-columns: 1fr; gap:8px;">
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Enter keywords...">
            </div>

            <div style="display:grid; grid-template-columns: 1fr; gap:12px; margin-top:12px;">
                <div class="form-group" style="display:flex; gap:12px; justify-content:flex-end;">
                    <button type="submit" class="btn-red" style="min-width: 100px; height: 36px; display: inline-flex; align-items: center; justify-content: center; vertical-align: top;"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('todos.index') }}" class="btn-gray" style="min-width: 100px; height: 36px; display: inline-flex; align-items: center; justify-content: center; vertical-align: top;">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="table-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; overflow:visible; position:relative; z-index:5;">
            @include('components.table-tools', [
                'tableId' => 'todosTable',
                'storageKey' => 'todos-columns',
                'columns' => [
                    'row_select' => 'Select',
                    'action' => 'Action',
                    'date_added' => 'Date Added',
                    'priority' => 'Priority',
                    'assigned_to' => 'Assigned To',
                    'task' => 'Task',
                    'what_to_do' => 'What To Do',
                    'deadline' => 'Deadline',
                    'status' => 'Status',
                ],
                'lockedColumns' => ['row_select'],
                'reportTitle' => 'Task Monitoring',
                'csvUrl' => request()->fullUrlWithQuery(['export' => 'csv']),
                'printUrl' => request()->fullUrlWithQuery(['export' => 'print']),
            ])
            <button type="button" class="btn-red" style="min-width: 100px; height: 36px; display: inline-flex; align-items: center; justify-content: center;" onclick="openTodoFormModal('todoCreateModal')"><i class="fas fa-plus"></i> Add New Task</button>
        </div>

        <div style="overflow-x:auto; max-width:100%;">
            <table id="todosTable" class="table table-hover" style="min-width:900px; width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:52px; border-bottom:2px solid #8b0000;">
                            <input type="checkbox" class="table-select-all" onclick="event.stopPropagation();">
                        </th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">ACTION</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">DATE ADDED</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:210px; border-bottom:2px solid #8b0000; position:relative;">
                            <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleTodoHeaderDropdown('todoPriorityDropdown', 'todoPriorityDropdownIcon', event)">
                                <span>PRIORITY</span>
                                <i class="fas fa-chevron-down" id="todoPriorityDropdownIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                            </div>
                            <div id="todoPriorityDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:140px; display:none; overflow:hidden;">
                                <a href="{{ request()->fullUrlWithQuery(['priority' => 'top']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Top</a>
                                <a href="{{ request()->fullUrlWithQuery(['priority' => 'high']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">High</a>
                                <a href="{{ request()->fullUrlWithQuery(['priority' => 'medium']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Medium</a>
                                <a href="{{ request()->fullUrlWithQuery(['priority' => 'low']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Low</a>
                                <a href="{{ request()->fullUrlWithQuery(['priority' => null]) }}" class="table-header-filter-link">All Priority</a>
                            </div>
                        </th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:140px; border-bottom:2px solid #8b0000; position:relative;">
                            <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleTodoHeaderDropdown('todoAssignedDropdown', 'todoAssignedDropdownIcon', event)">
                                <span>ASSIGNED TO</span>
                                <i class="fas fa-chevron-down" id="todoAssignedDropdownIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                            </div>
                            <div id="todoAssignedDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:150px; display:none; overflow:hidden;">
                                @foreach($assignedToOptions as $person)
                                    <a href="{{ request()->fullUrlWithQuery(['assigned_to' => $person]) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">{{ $person }}</a>
                                @endforeach
                                <a href="{{ request()->fullUrlWithQuery(['assigned_to' => null]) }}" class="table-header-filter-link">All Assigned</a>
                            </div>
                        </th>
                        <th style="text-align:center; padding:12px 8px; min-width:200px; border-bottom:2px solid #8b0000; position:relative;">
                            <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleTodoHeaderDropdown('todoTaskDropdown', 'todoTaskDropdownIcon', event)">
                                <span>TASK</span>
                                <i class="fas fa-chevron-down" id="todoTaskDropdownIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                            </div>
                            <div id="todoTaskDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:168px; display:none; overflow:hidden;">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'newest']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Newest to Oldest</a>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'oldest']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Oldest to Newest</a>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'az']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">A-Z</a>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'za']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Z-A</a>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => null]) }}" class="table-header-filter-link">Default Order</a>
                            </div>
                        </th>
                        <th style="text-align:center; padding:12px 8px; min-width:250px; border-bottom:2px solid #8b0000;">WHAT TO DO</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">DEADLINE</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:210px; border-bottom:2px solid #8b0000; position:relative;">
                            <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleTodoHeaderDropdown('todoStatusDropdown', 'todoStatusDropdownIcon', event)">
                                <span>STATUS</span>
                                <i class="fas fa-chevron-down" id="todoStatusDropdownIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                            </div>
                            <div id="todoStatusDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:150px; display:none; overflow:hidden;">
                                <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Pending</a>
                                <a href="{{ request()->fullUrlWithQuery(['status' => 'on-going']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">On-going</a>
                                <a href="{{ request()->fullUrlWithQuery(['status' => 'done']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Done</a>
                                <a href="{{ request()->fullUrlWithQuery(['status' => 'cancelled']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Cancelled</a>
                                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="table-header-filter-link">All Status</a>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todos as $index => $todo)

                    <tr id="todoRow-{{ $todo->id }}" class="clickable-row {{ ($todo->date_added && $todo->date_added < now() && $todo->status != 'completed') ? 'overdue-row' : '' }}" data-href="{{ route('todos.show', $todo) }}" style="cursor: pointer;">
                        <td style="text-align:center; padding:20px 8px; white-space:nowrap; width:52px;" onclick="event.stopPropagation();">
                            <input type="checkbox" class="table-row-select">
                        </td>

                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;" onclick="event.stopPropagation();">
                            <div style="display:flex; gap:4px; align-items:center; justify-content:flex-start;">
                                <button
                                    type="button"
                                    class="btn-blue"
                                    title="Edit"
                                    style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;"
                                    data-todo-id="{{ $todo->id }}"
                                    data-todo-title="{{ e($todo->title) }}"
                                    data-todo-description="{{ e($todo->description ?? '') }}"
                                    data-todo-priority="{{ $todo->priority }}"
                                    data-todo-status="{{ $todo->status }}"
                                    data-todo-assigned="{{ e($todo->assigned_to ?? '') }}"
                                    data-todo-date-added="{{ $todo->date_added ? $todo->date_added->format('Y-m-d') : '' }}"
                                    data-todo-due-date="{{ $todo->due_date ? $todo->due_date->format('Y-m-d') : '' }}"
                                    data-todo-remarks="{{ e($todo->remarks ?? '') }}"
                                    data-todo-is-recurring="{{ $todo->is_recurring ? '1' : '0' }}"
                                    data-todo-recurrence-frequency="{{ $todo->recurrence_frequency ?? 'weekly' }}"
                                    data-todo-recurrence-interval="{{ $todo->recurrence_interval ?? 1 }}"
                                    data-todo-recurrence-end-date="{{ $todo->recurrence_end_date ? $todo->recurrence_end_date->format('Y-m-d') : '' }}"
                                    onclick="event.stopPropagation(); openTodoEditModal(this)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($todo->pinnedByCurrentUser())
                                    <span title="Pinned" style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; border-radius:8px; background:#fff7ed; color:#c2410c; border:1px solid #fdba74;">
                                        <i class="fas fa-thumbtack"></i>
                                    </span>
                                @endif
                                @if(auth()->user()?->isAdmin())
                                    <form action="{{ route('todos.destroy', $todo) }}" method="POST" style="display:inline;" id="deleteForm-{{ $todo->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn-danger" title="Delete" onclick="event.stopPropagation(); confirmDelete({{ $todo->id }}, '{{ $todo->title }}')" style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>

                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;">{{ $todo->date_added ? $todo->date_added->format('n-j-Y') : ($todo->created_at ? $todo->created_at->format('n-j-Y') : 'No date') }}</td>

                        <!-- PRIORITY -->
                        <td style="text-align:center; padding:20px 16px; white-space:nowrap; width:210px;" onclick="event.stopPropagation();">
                            <select 
                                class="form-control inline-select 
                                {{ match($todo->priority) {
                                    'top' => 'badge-top',
                                    'high' => 'badge-high',
                                    'medium' => 'badge-medium',
                                    'low' => 'badge-low',
                                    default => ''
                                } }}"
                                onchange="changePriority(this, {{ $todo->id }}, this.value)">

                                <option value="top" {{ $todo->priority=='top'?'selected':'' }}>TOP</option>
                                <option value="high" {{ $todo->priority=='high'?'selected':'' }}>HIGH</option>
                                <option value="medium" {{ $todo->priority=='medium'?'selected':'' }}>MEDIUM</option>
                                <option value="low" {{ $todo->priority=='low'?'selected':'' }}>LOW</option>
                            </select>
                        </td>

                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;">{{ $todo->assigned_to ?? 'Unassigned' }}</td>

                        <td style="text-align:left; padding:20px 20px 20px 20px; min-width:200px; word-wrap:break-word; font-size: 13px; font-weight: 600;">{{ $todo->title }}</td>

                        <td style="text-align:left; padding:20px 20px 20px 20px; min-width:250px; word-wrap:break-word; font-size: 12px; color: #6c757d; white-space: pre-line;">{{ $todo->description ?? 'No description' }}</td>

                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;">{{ $todo->date_added?->format('M d, Y') ?? '—' }}</td>

                        <!-- STATUS -->
                        <td style="text-align:center; padding:20px 16px; white-space:nowrap; width:210px;" onclick="event.stopPropagation();">
                            <select 
                                class="form-control inline-select 
                                {{ match($todo->status) {
                                    'pending' => 'status-pending',
                                    'on-going' => 'status-ongoing',
                                    'done' => 'status-done',
                                    'cancelled' => 'status-cancelled',
                                    default => ''
                                } }}"
                                onchange="changeStatus(this, {{ $todo->id }}, this.value)"
                            >
                                <option value="pending" {{ $todo->status=='pending'?'selected':'' }}>PENDING</option>
                                <option value="on-going" {{ $todo->status=='on-going'?'selected':'' }}>ON-GOING</option>
                                <option value="done" {{ $todo->status=='done'?'selected':'' }}>DONE</option>
                                <option value="cancelled" {{ $todo->status=='cancelled'?'selected':'' }}>CANCELLED</option>
                            </select>
                        </td>

                    </tr>

                    @empty
                    <tr>
                        <td colspan="10" style="text-align:center; padding:60px;">
                            <div style="background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%); border:2px dashed rgba(192,57,43,0.2); border-radius:16px; padding:40px;">
                                <i class="fas fa-tasks" style="font-size:48px; color:#c0392b; margin-bottom:16px;"></i>
                                <h3 style="margin-bottom:8px; color:#1a1a2e;">No Tasks Found</h3>
                                <p style="margin-bottom:20px; color:#64748b;">Start by adding your first task.</p>
                                <button type="button" class="btn-red" onclick="openTodoFormModal('todoCreateModal')">
                                    <i class="fas fa-plus"></i> Add Task
                                </button>
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

    <div id="todoCreateModal" class="financial-form-modal" style="display:none;">
        <div class="financial-form-modal__dialog" style="max-width:960px;">
            <div class="financial-form-modal__header">
                <div>
                    <div class="financial-form-modal__title">Create Todo</div>
                    <div class="financial-form-modal__subtitle">Add a new task without leaving the monitoring page.</div>
                </div>
                <button type="button" onclick="closeTodoFormModal('todoCreateModal')" title="Close" aria-label="Close" style="border:none; background:transparent; color:#64748b; font-size:18px; font-weight:700; line-height:1; padding:0; width:24px; height:24px; display:flex; align-items:center; justify-content:center; cursor:pointer;">&times;</button>
            </div>
            @include('todos._form', [
                'assignedToOptions' => $assignedToOptions,
                'formMode' => 'create',
                'isModal' => true,
                'modalId' => 'todoCreateModal',
                'returnUrl' => request()->fullUrl(),
            ])
        </div>
    </div>

    <div id="todoEditModal" class="financial-form-modal" style="display:none;">
        <div class="financial-form-modal__dialog" style="max-width:960px;">
            <div class="financial-form-modal__header">
                <div>
                    <div class="financial-form-modal__title">Edit Todo</div>
                    <div class="financial-form-modal__subtitle" id="todoEditModalSubtitle">Update the selected task without leaving the monitoring page.</div>
                </div>
                <button type="button" onclick="closeTodoFormModal('todoEditModal')" title="Close" aria-label="Close" style="border:none; background:transparent; color:#64748b; font-size:18px; font-weight:700; line-height:1; padding:0; width:24px; height:24px; display:flex; align-items:center; justify-content:center; cursor:pointer;">&times;</button>
            </div>
            @include('todos._form', [
                'todo' => new \App\Models\Todo(),
                'assignedToOptions' => $assignedToOptions,
                'formMode' => 'edit',
                'isModal' => true,
                'modalId' => 'todoEditModal',
                'formAction' => '#',
                'returnUrl' => request()->fullUrl(),
            ])
        </div>
    </div>


    <script>
        function closeTodoHeaderDropdowns() {
            ['todoPriorityDropdown', 'todoAssignedDropdown', 'todoTaskDropdown', 'todoStatusDropdown'].forEach((id) => {
                const dropdown = document.getElementById(id);
                if (dropdown) {
                    dropdown.style.display = 'none';
                }
            });

            ['todoPriorityDropdownIcon', 'todoAssignedDropdownIcon', 'todoTaskDropdownIcon', 'todoStatusDropdownIcon'].forEach((id) => {
                const icon = document.getElementById(id);
                if (icon) {
                    icon.style.transform = 'rotate(0deg)';
                }
            });
        }

        function toggleTodoHeaderDropdown(dropdownId, iconId, event) {
            if (event) {
                event.stopPropagation();
            }

            const dropdown = document.getElementById(dropdownId);
            const icon = document.getElementById(iconId);
            const isOpen = dropdown && dropdown.style.display === 'block';

            closeTodoHeaderDropdowns();

            if (dropdown && icon && !isOpen) {
                dropdown.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
            }
        }

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

        document.addEventListener('click', function() {
            closeTodoHeaderDropdowns();
        });

        // Delete confirmation function
        function confirmDelete(id, title){
            window.showConfirmDialog({
                title: 'Delete Task',
                message: `Are you sure you want to delete <strong>${title}</strong>? This cannot be undone.`,
                confirmText: 'Delete',
                cancelText: 'Cancel',
                confirmClass: 'notification-btn-confirm',
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
                            window.showNotification({type: 'success', title: 'Task deleted', message: 'Task has been successfully deleted.'});
                            return;
                        }

                        const errorMessage = result.data.message || result.data.error || 'Failed to delete the task.';
                        window.showNotification({type: 'danger', title: 'Delete Failed', message: errorMessage});
                    })
                    .catch(() => {
                        window.showNotification({type: 'danger', title: 'Delete Failed', message: 'An error occurred while deleting the task. Please try again.'});
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
            if(value === 'on-going') el.classList.add('status-ongoing');
            if(value === 'done') el.classList.add('status-done');
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
                console.log('Status update response:', data);
                if(data.success){
                    window.showNotification({type: 'success', title: 'Status Updated', message: `Status updated to ${value}`});
                    // Refresh the specific row to show updated status
                    const row = document.getElementById(`todoRow-${id}`);
                    if(row) {
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                } else {
                    console.error('Status update failed:', data);
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
                    window.showNotification({type: 'success', title: 'Priority Updated', message: `Priority updated to ${value}`});
                }
            });
        }

    </script>

    <script>
        function toggleTodoReminderList() {
            const list = document.getElementById('todoReminderList');
            const label = document.getElementById('todoReminderToggleLabel');

            if (!list || !label) {
                return;
            }

            const isHidden = list.style.display === 'none';
            list.style.display = isHidden ? 'grid' : 'none';
            label.textContent = isHidden ? 'Hide List' : 'View List';
        }

        function openTodoFormModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) {
                return;
            }

            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function openTodoEditModal(button) {
            const modal = document.getElementById('todoEditModal');
            if (!modal || !button) {
                return;
            }

            const todoId = button.dataset.todoId;
            const form = modal.querySelector('form');
            if (!todoId || !form) {
                return;
            }

            form.action = `/todos/${todoId}`;

            const subtitle = document.getElementById('todoEditModalSubtitle');
            if (subtitle) {
                subtitle.textContent = button.dataset.todoTitle || 'Update the selected task without leaving the monitoring page.';
            }

            const setValue = (name, value) => {
                const field = form.querySelector(`[name="${name}"]`);
                if (!field) return;
                field.value = value ?? '';
            };

            const setChecked = (name, checked) => {
                const field = form.querySelector(`[name="${name}"]`);
                if (!field) return;
                field.checked = checked;
            };

            setValue('title', button.dataset.todoTitle);
            setValue('description', button.dataset.todoDescription);
            setValue('priority', button.dataset.todoPriority);
            setValue('status', button.dataset.todoStatus);
            setValue('assigned_to', button.dataset.todoAssigned);
            setValue('date_added', button.dataset.todoDateAdded);
            setValue('due_date', button.dataset.todoDueDate);
            setValue('remarks', button.dataset.todoRemarks);
            setValue('recurrence_frequency', button.dataset.todoRecurrenceFrequency || 'weekly');
            setValue('recurrence_interval', button.dataset.todoRecurrenceInterval || 1);
            setValue('recurrence_end_date', button.dataset.todoRecurrenceEndDate);
            setValue('modal_record_id', todoId);
            setChecked('is_recurring', button.dataset.todoIsRecurring === '1');

            openTodoFormModal('todoEditModal');
        }

        function closeTodoFormModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) {
                return;
            }

            modal.style.display = 'none';
            document.body.style.overflow = '';
        }

        document.addEventListener('click', function(event) {
            const modal = event.target.closest('.financial-form-modal');
            if (modal && event.target === modal) {
                closeTodoFormModal(modal.id);
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key !== 'Escape') {
                return;
            }

            document.querySelectorAll('.financial-form-modal').forEach(function(modal) {
                if (modal.style.display === 'block') {
                    closeTodoFormModal(modal.id);
                }
            });
        });

        @if($errors->any())
            window.addEventListener('load', function() {
                @if(old('modal_mode') === 'edit' && old('modal_record_id'))
                    const editButton = document.querySelector('[data-todo-id="{{ old('modal_record_id') }}"]');
                    if (editButton) {
                        openTodoEditModal(editButton);
                    }
                @else
                    openTodoFormModal('todoCreateModal');
                @endif
            });
        @endif
    </script>
</x-app-layout>
