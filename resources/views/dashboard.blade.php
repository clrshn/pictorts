<x-app-layout>
    <x-slot name="header">
        <h1>Dashboard</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / Dashboard</div>
    </x-slot>

    @php
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    @endphp

    <div class="dashboard-shell">
        <section class="dashboard-hero">
            <div class="dashboard-hero-copy">
                <div class="dashboard-kicker">PICTO - Provincial Information and Communications Technology Office</div>
                

                <form method="GET" action="{{ route('documents.index') }}" class="dashboard-search">
                    <div class="dashboard-search-field">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" class="form-control" placeholder="Search documents, transaction numbers, or subjects">
                    </div>
                    <button type="submit" class="btn-red">
                        <i class="fas fa-arrow-right"></i> Search
                    </button>
                </form>
            </div>

            <div class="dashboard-hero-panel">
                <div class="hero-panel-label">Today’s Focus</div>
                <div class="hero-panel-grid">
                    <div class="hero-chip">
                        <span class="hero-chip-title">Pending Tasks</span>
                        <strong>{{ $todoPending }}</strong>
                    </div>
                    <div class="hero-chip">
                        <span class="hero-chip-title">Due Today</span>
                        <strong>{{ $todoDueToday }}</strong>
                    </div>
                    <div class="hero-chip">
                        <span class="hero-chip-title">Overdue</span>
                        <strong>{{ $todoOverdue }}</strong>
                    </div>
                    <div class="hero-chip">
                        <span class="hero-chip-title">Documents This Year</span>
                        <strong>{{ $totalDocuments }}</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-section">
            <div class="dashboard-section-title">
                <span>Document Overview</span>
                <small>Incoming and outgoing movement</small>
            </div>

            <div class="dashboard-card-grid dashboard-card-grid--three">
                <a href="{{ route('documents.index') }}" class="overview-card overview-card--sunrise">
                    <div>
                        <div class="overview-card-label">All Documents</div>
                        <div class="overview-card-value">{{ $totalDocuments }}</div>
                        <div class="overview-card-meta">{{ $docOngoing }} ongoing, {{ $docCompleted }} completed</div>
                    </div>
                    <div class="overview-card-icon"><i class="fas fa-folder-open"></i></div>
                </a>

                <a href="{{ route('documents.index', ['direction' => 'INCOMING']) }}" class="overview-card overview-card--sky">
                    <div>
                        <div class="overview-card-label">Incoming</div>
                        <div class="overview-card-value">{{ $incomingCount }}</div>
                        <div class="overview-card-meta">For receiving and routing</div>
                    </div>
                    <div class="overview-card-icon"><i class="fas fa-arrow-down"></i></div>
                </a>

                <a href="{{ route('documents.index', ['direction' => 'OUTGOING']) }}" class="overview-card overview-card--mint">
                    <div>
                        <div class="overview-card-label">Outgoing</div>
                        <div class="overview-card-value">{{ $outgoingCount }}</div>
                        <div class="overview-card-meta">Released outside the office</div>
                    </div>
                    <div class="overview-card-icon"><i class="fas fa-arrow-up"></i></div>
                </a>
            </div>
        </section>

        <section class="dashboard-section">
            <div class="dashboard-section-title">
                <span>Financial Overview</span>
                <small>Monitoring across statuses</small>
            </div>

            <div class="dashboard-card-grid dashboard-card-grid--four">
                <a href="{{ route('financial.index') }}" class="overview-card overview-card--peach">
                    <div>
                        <div class="overview-card-label">All Financial</div>
                        <div class="overview-card-value">{{ $totalFinancial }}</div>
                        <div class="overview-card-meta">Total financial records</div>
                    </div>
                    <div class="overview-card-icon"><i class="fas fa-coins"></i></div>
                </a>

                <a href="{{ route('financial.index', ['status' => 'ACTIVE']) }}" class="overview-card overview-card--seafoam">
                    <div>
                        <div class="overview-card-label">Active</div>
                        <div class="overview-card-value">{{ $financialActive }}</div>
                        <div class="overview-card-meta">Currently in progress</div>
                    </div>
                    <div class="overview-card-icon"><i class="fas fa-play"></i></div>
                </a>

                <a href="{{ route('financial.index', ['status' => 'CANCELLED']) }}" class="overview-card overview-card--rose">
                    <div>
                        <div class="overview-card-label">Cancelled</div>
                        <div class="overview-card-value">{{ $financialCancelled }}</div>
                        <div class="overview-card-meta">Stopped or voided records</div>
                    </div>
                    <div class="overview-card-icon"><i class="fas fa-ban"></i></div>
                </a>

                <a href="{{ route('financial.index', ['status' => 'FINISHED']) }}" class="overview-card overview-card--ice">
                    <div>
                        <div class="overview-card-label">Finished</div>
                        <div class="overview-card-value">{{ $financialFinished }}</div>
                        <div class="overview-card-meta">Completed transactions</div>
                    </div>
                    <div class="overview-card-icon"><i class="fas fa-flag-checkered"></i></div>
                </a>
            </div>
        </section>

        <section class="dashboard-detail-grid">
            <div class="dashboard-analytics-card">
                <div class="dashboard-section-title dashboard-section-title--tight">
                    <span>Activity Trend</span>
                    <small>Monthly documents and financial records</small>
                </div>
                <canvas id="monthlyChart" height="120"></canvas>
            </div>

            <div class="dashboard-reminder-card">
                <div class="dashboard-section-title dashboard-section-title--tight">
                    <span>To-Do Reminder</span>
                    <small>Upcoming and overdue tasks</small>
                </div>

                <div class="reminder-summary">
                    <a href="{{ route('todos.index', ['status' => 'pending']) }}" class="reminder-pill reminder-pill--blue">
                        <strong>{{ $todoPending }}</strong>
                        <span>Open Tasks</span>
                    </a>
                    <a href="{{ route('todos.index') }}" class="reminder-pill reminder-pill--red">
                        <strong>{{ $todoOverdue }}</strong>
                        <span>Need Attention</span>
                    </a>
                </div>

                <div class="reminder-list">
                    @forelse($todoReminders as $todo)
                        @php
                            $isOverdue = $todo->due_date && $todo->due_date->isPast();
                            $isToday = $todo->due_date && $todo->due_date->isToday();
                        @endphp
                        <a href="{{ route('todos.show', $todo) }}" class="reminder-item {{ $isOverdue ? 'is-overdue' : ($isToday ? 'is-today' : '') }}">
                            <div class="reminder-item-main">
                                <div class="reminder-title">{{ $todo->title }}</div>
                                <div class="reminder-meta">
                                    <span>{{ strtoupper($todo->priority ?? 'medium') }}</span>
                                    <span>{{ $todo->assigned_to ?? 'Unassigned' }}</span>
                                </div>
                            </div>
                            <div class="reminder-date">
                                @if($todo->due_date)
                                    {{ $todo->due_date->format('M d') }}
                                @else
                                    No due date
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="reminder-empty">
                            <i class="fas fa-check-circle"></i>
                            <p>No pending reminders right now.</p>
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('todos.index') }}" class="reminder-footer-link">
                    View full task board <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartCanvas = document.getElementById('monthlyChart');
        const ctx = chartCanvas.getContext('2d');
        const blueRedFill = ctx.createLinearGradient(0, 0, chartCanvas.width || 600, 260);
        blueRedFill.addColorStop(0, 'rgba(59, 130, 246, 0.28)');
        blueRedFill.addColorStop(0.5, 'rgba(96, 165, 250, 0.16)');
        blueRedFill.addColorStop(1, 'rgba(239, 68, 68, 0.20)');

        const redBlueStroke = ctx.createLinearGradient(0, 0, chartCanvas.width || 600, 0);
        redBlueStroke.addColorStop(0, '#2563eb');
        redBlueStroke.addColorStop(1, '#dc2626');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($monthLabels),
                datasets: [
                    {
                        label: 'Documents',
                        data: @json($chartDocuments),
                        borderColor: redBlueStroke,
                        backgroundColor: blueRedFill,
                        fill: true,
                        tension: 0.38,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBorderWidth: 0,
                        pointBackgroundColor: '#2563eb',
                    },
                    {
                        label: 'Financial',
                        data: @json($chartFinancial),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0)',
                        fill: false,
                        tension: 0.38,
                        borderWidth: 2,
                        borderDash: [8, 6],
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        pointBorderWidth: 0,
                        pointBackgroundColor: '#ef4444',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 10,
                            color: '#475569',
                            padding: 18
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.12)'
                        },
                        ticks: {
                            color: '#64748b',
                            stepSize: 20
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(148, 163, 184, 0.08)'
                        },
                        ticks: {
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    </script>

    <style>
        .dashboard-shell {
            display: grid;
            gap: 24px;
        }

        .dashboard-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(280px, 0.9fr);
            gap: 24px;
            padding: 28px;
            border-radius: 26px;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.18), transparent 34%),
                radial-gradient(circle at bottom right, rgba(239, 68, 68, 0.14), transparent 30%),
                linear-gradient(135deg, rgba(255,255,255,0.96) 0%, rgba(248,250,252,0.94) 100%);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
        }

        .dashboard-kicker {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #2563eb;
            margin-bottom: 10px;
        }

        .dashboard-hero-copy h2 {
            margin: 0 0 10px;
            font-size: clamp(28px, 3vw, 38px);
            line-height: 1.1;
            color: #0f172a;
        }

        .dashboard-hero-copy p {
            margin: 0;
            max-width: 720px;
            color: #64748b;
            font-size: 15px;
            line-height: 1.7;
        }

        .dashboard-search {
            display: flex;
            gap: 14px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 22px;
        }

        .dashboard-search-field {
            flex: 1;
            min-width: 280px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 16px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 16px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.8);
        }

        .dashboard-search-field i {
            color: #94a3b8;
            font-size: 14px;
        }

        .dashboard-search-field input {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            padding-left: 0 !important;
        }

        .dashboard-hero-panel {
            padding: 22px;
            border-radius: 22px;
            background: linear-gradient(160deg, rgba(255,255,255,0.82) 0%, rgba(241,245,249,0.95) 100%);
            border: 1px solid rgba(148, 163, 184, 0.18);
        }

        .hero-panel-label {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
        }

        .hero-panel-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .hero-chip {
            padding: 16px;
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(255,255,255,0.85) 0%, rgba(248,250,252,0.92) 100%);
            border: 1px solid rgba(191, 219, 254, 0.55);
        }

        .hero-chip-title {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .hero-chip strong {
            font-size: 28px;
            color: #0f172a;
            font-weight: 800;
        }

        .dashboard-section {
            display: grid;
            gap: 14px;
        }

        .dashboard-section-title {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 12px;
        }

        .dashboard-section-title span {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
        }

        .dashboard-section-title small {
            color: #64748b;
            font-size: 13px;
        }

        .dashboard-section-title--tight {
            margin-bottom: 16px;
        }

        .dashboard-card-grid {
            display: grid;
            gap: 16px;
        }

        .dashboard-card-grid--three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .dashboard-card-grid--four {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .overview-card {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            padding: 24px;
            min-height: 150px;
            border-radius: 24px;
            text-decoration: none;
            color: #0f172a;
            border: 1px solid rgba(255,255,255,0.45);
            box-shadow: 0 18px 30px rgba(15, 23, 42, 0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .overview-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 42px rgba(15, 23, 42, 0.12);
        }

        .overview-card-label {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(15, 23, 42, 0.72);
            margin-bottom: 10px;
        }

        .overview-card-value {
            font-size: 42px;
            line-height: 1;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .overview-card-meta {
            font-size: 13px;
            line-height: 1.5;
            color: rgba(15, 23, 42, 0.68);
        }

        .overview-card-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.65);
            color: #0f172a;
            font-size: 22px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.8);
        }

        .overview-card--sunrise {
            background: linear-gradient(135deg, #fff5e8 0%, #ffe7ca 52%, #ffd5d0 100%);
        }

        .overview-card--sky {
            background: linear-gradient(135deg, #e9f2ff 0%, #d8e9ff 50%, #d6f1ff 100%);
        }

        .overview-card--mint {
            background: linear-gradient(135deg, #ebfbf4 0%, #d5f7ea 55%, #cceee8 100%);
        }

        .overview-card--peach {
            background: linear-gradient(135deg, #fff1eb 0%, #ffe0d9 55%, #ffd6c2 100%);
        }

        .overview-card--seafoam {
            background: linear-gradient(135deg, #edfdf5 0%, #d5fae7 52%, #c8f2e6 100%);
        }

        .overview-card--rose {
            background: linear-gradient(135deg, #fff0f3 0%, #ffdbe5 55%, #ffd1d9 100%);
        }

        .overview-card--ice {
            background: linear-gradient(135deg, #eef8ff 0%, #dcefff 55%, #d7ebff 100%);
        }

        .dashboard-detail-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(320px, 0.9fr);
            gap: 18px;
        }

        .dashboard-analytics-card,
        .dashboard-reminder-card {
            padding: 22px;
            border-radius: 24px;
            background: linear-gradient(180deg, rgba(255,255,255,0.96) 0%, rgba(248,250,252,0.98) 100%);
            border: 1px solid rgba(148, 163, 184, 0.16);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.08);
        }

        .dashboard-analytics-card {
            min-height: 420px;
        }

        #monthlyChart {
            width: 100% !important;
            height: 320px !important;
        }

        .reminder-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .reminder-pill {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 16px;
            text-decoration: none;
            border-radius: 18px;
            color: #0f172a;
            border: 1px solid rgba(255,255,255,0.4);
        }

        .reminder-pill strong {
            font-size: 28px;
            line-height: 1;
        }

        .reminder-pill span {
            font-size: 13px;
            color: rgba(15, 23, 42, 0.72);
        }

        .reminder-pill--blue {
            background: linear-gradient(135deg, #e8f1ff 0%, #dbeafe 100%);
        }

        .reminder-pill--red {
            background: linear-gradient(135deg, #ffe8ec 0%, #ffe0e7 100%);
        }

        .reminder-list {
            display: grid;
            gap: 10px;
        }

        .reminder-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 16px;
            text-decoration: none;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .reminder-item:hover {
            transform: translateX(3px);
            border-color: rgba(96, 165, 250, 0.45);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
        }

        .reminder-item.is-overdue {
            background: linear-gradient(135deg, rgba(254, 242, 242, 0.96) 0%, rgba(255,255,255,1) 100%);
            border-color: rgba(248, 113, 113, 0.36);
        }

        .reminder-item.is-today {
            background: linear-gradient(135deg, rgba(239, 246, 255, 0.96) 0%, rgba(255,255,255,1) 100%);
            border-color: rgba(96, 165, 250, 0.36);
        }

        .reminder-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .reminder-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            font-size: 12px;
            color: #64748b;
        }

        .reminder-date {
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            color: #334155;
        }

        .reminder-empty {
            display: grid;
            place-items: center;
            gap: 8px;
            padding: 28px 16px;
            border-radius: 18px;
            color: #64748b;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            border: 1px dashed rgba(148, 163, 184, 0.32);
            text-align: center;
        }

        .reminder-empty i {
            font-size: 22px;
            color: #2563eb;
        }

        .reminder-footer-link {
            margin-top: 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #2563eb;
            font-size: 13px;
            font-weight: 700;
        }

        @media (max-width: 1200px) {
            .dashboard-card-grid--four {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-detail-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .dashboard-hero,
            .dashboard-card-grid--three {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .dashboard-shell {
                gap: 18px;
            }

            .dashboard-hero,
            .dashboard-analytics-card,
            .dashboard-reminder-card {
                padding: 18px;
                border-radius: 20px;
            }

            .dashboard-card-grid--four,
            .reminder-summary,
            .hero-panel-grid {
                grid-template-columns: 1fr;
            }

            .overview-card {
                min-height: auto;
            }
        }
    </style>
</x-app-layout>
