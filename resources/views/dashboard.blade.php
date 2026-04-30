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
                <div class="hero-inline-stats">
                    <div class="hero-inline-stat">
                        <span>Ongoing Docs</span>
                        <strong>{{ $docOngoing }}</strong>
                    </div>
                    <div class="hero-inline-stat">
                        <span>Active Financial</span>
                        <strong>{{ $financialActive }}</strong>
                    </div>
                    <div class="hero-inline-stat">
                        <span>Pending Approvals</span>
                        <strong>{{ $approvalPendingCount }}</strong>
                    </div>
                </div>

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
                        <span class="hero-chip-title">Due Tomorrow</span>
                        <strong>{{ $todoDueTomorrow }}</strong>
                    </div>
                    <div class="hero-chip">
                        <span class="hero-chip-title">Overdue</span>
                        <strong>{{ $todoOverdue }}</strong>
                    </div>
                    <div class="hero-chip">
                        <span class="hero-chip-title">Next 7 Days</span>
                        <strong>{{ $todoDueThisWeek }}</strong>
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
                        <div class="overview-card-meta">{{ $docOngoing }} ongoing, {{ $docCompleted }} done</div>
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
                        <div class="overview-card-meta">Done transactions</div>
                    </div>
                    <div class="overview-card-icon"><i class="fas fa-flag-checkered"></i></div>
                </a>
            </div>
        </section>

        <section class="dashboard-section">
            <div class="dashboard-panel dashboard-panel--wide">
                <div class="dashboard-section-title dashboard-section-title--tight">
                    <span>Activity Trend</span>
                    <small>Monthly documents and financial records</small>
                </div>
                <div class="dashboard-chart-shell">
                    <canvas id="monthlyChart" height="160"></canvas>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartCanvas = document.getElementById('monthlyChart');
        const ctx = chartCanvas.getContext('2d');
        const blueRedFill = ctx.createLinearGradient(0, 0, chartCanvas.width || 900, 320);
        blueRedFill.addColorStop(0, 'rgba(59, 130, 246, 0.24)');
        blueRedFill.addColorStop(0.5, 'rgba(96, 165, 250, 0.12)');
        blueRedFill.addColorStop(1, 'rgba(239, 68, 68, 0.16)');

        const redBlueStroke = ctx.createLinearGradient(0, 0, chartCanvas.width || 900, 0);
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
            gap: 18px;
            width: 100%;
            max-width: 1540px;
            margin: 0 auto;
            padding: 0 10px 14px;
            box-sizing: border-box;
            overflow-x: hidden;
        }

        .dashboard-section,
        .dashboard-hero,
        .dashboard-panel,
        .dashboard-card-grid--three,
        .dashboard-card-grid--four,
        .dashboard-hero-copy,
        .dashboard-hero-panel,
        .hero-inline-stats,
        .hero-panel-grid,
        .overview-card {
            min-width: 0;
        }

        .dashboard-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(320px, 0.92fr);
            gap: 16px;
            padding: 18px 20px;
            border-radius: 24px;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.18), transparent 34%),
                radial-gradient(circle at bottom right, rgba(239, 68, 68, 0.14), transparent 30%),
                linear-gradient(135deg, rgba(255,255,255,0.96) 0%, rgba(248,250,252,0.94) 100%);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
        }

        .dashboard-kicker {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #2563eb;
            margin-bottom: 12px;
        }

        .hero-inline-stats,
        .dashboard-card-grid--three,
        .dashboard-card-grid--four,
        .hero-panel-grid {
            display: grid;
            gap: 10px;
        }

        .hero-inline-stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-bottom: 14px;
        }

        .hero-inline-stat,
        .hero-chip {
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(255,255,255,0.92);
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .hero-inline-stat span,
        .hero-chip-title,
        .overview-card-label,
        .overview-card-meta,
        .hero-panel-label {
            color: #64748b;
        }

        .hero-inline-stat span,
        .hero-chip-title,
        .overview-card-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .hero-inline-stat strong,
        .hero-chip strong {
            font-size: 30px;
            line-height: 1;
            color: #0f172a;
        }

        .dashboard-search {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .dashboard-search-field {
            flex: 1 1 380px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 16px;
            height: 52px;
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(255,255,255,0.95);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.65);
        }

        .dashboard-search-field i {
            color: #64748b;
        }

        .dashboard-search-field input {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 0 !important;
            height: auto !important;
        }

        .dashboard-hero-panel {
            border-radius: 20px;
            padding: 16px;
            background: linear-gradient(135deg, rgba(255,255,255,0.94), rgba(248,250,252,0.96));
            border: 1px solid rgba(148, 163, 184, 0.16);
        }

        .hero-panel-label {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .hero-panel-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-section {
            display: grid;
            gap: 10px;
        }

        .dashboard-section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .dashboard-section-title span {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
        }

        .dashboard-section-title small {
            font-size: 12px;
            color: #64748b;
        }

        .dashboard-section-title--tight {
            margin-bottom: 10px;
        }

        .dashboard-card-grid--three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .dashboard-card-grid--four {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .overview-card {
            min-height: 114px;
            border-radius: 22px;
            padding: 20px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: inherit;
            border: 1px solid rgba(148, 163, 184, 0.14);
            box-shadow: 0 16px 28px rgba(15, 23, 42, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
        }

        .overview-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 32px rgba(15, 23, 42, 0.08);
        }

        .overview-card-value {
            font-size: 40px;
            font-weight: 800;
            line-height: 1;
            color: #0f172a;
            margin: 6px 0 8px;
        }

        .overview-card-meta {
            font-size: 12px;
        }

        .overview-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: rgba(255,255,255,0.85);
            display: grid;
            place-items: center;
            font-size: 18px;
            color: #0f172a;
            flex-shrink: 0;
        }

        .overview-card--sunrise { background: linear-gradient(135deg, rgba(255, 245, 225, 0.98), rgba(255, 217, 217, 0.96)); }
        .overview-card--sky { background: linear-gradient(135deg, rgba(227, 240, 255, 0.98), rgba(198, 222, 255, 0.96)); }
        .overview-card--mint { background: linear-gradient(135deg, rgba(223, 250, 238, 0.98), rgba(203, 244, 230, 0.96)); }
        .overview-card--peach { background: linear-gradient(135deg, rgba(255, 234, 226, 0.98), rgba(255, 214, 207, 0.96)); }
        .overview-card--seafoam { background: linear-gradient(135deg, rgba(221, 248, 234, 0.98), rgba(196, 241, 221, 0.96)); }
        .overview-card--rose { background: linear-gradient(135deg, rgba(255, 229, 237, 0.98), rgba(255, 208, 224, 0.96)); }
        .overview-card--ice { background: linear-gradient(135deg, rgba(227, 239, 255, 0.98), rgba(209, 228, 250, 0.96)); }

        .dashboard-panel {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 24px;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
            padding: 20px 22px;
            overflow: hidden;
        }

        .dashboard-chart-shell {
            position: relative;
            min-height: 420px;
        }

        @media (max-width: 1400px) {
            .dashboard-hero,
            .dashboard-card-grid--four {
                grid-template-columns: 1fr;
            }

            .dashboard-card-grid--three {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .dashboard-hero,
            .hero-inline-stats,
            .hero-panel-grid,
            .dashboard-card-grid--three,
            .dashboard-card-grid--four {
                grid-template-columns: 1fr;
            }

            .dashboard-search {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</x-app-layout>
