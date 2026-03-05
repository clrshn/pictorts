<x-app-layout>
    <x-slot name="header">
        <h1>Dashboard</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / Dashboard</div>
    </x-slot>

    <!-- Search Bar -->
    <div class="filter-box" style="margin-bottom:16px;">
        <h3><i class="fas fa-search"></i> Quick Search</h3>
        <form method="GET" action="{{ route('documents.index') }}" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
            <div class="form-group" style="margin:0; flex:1; min-width:280px;">
                <label>Search Documents</label>
                <input type="text" name="search" class="form-control" placeholder="Search by Document No., Tracking Code, Transaction Number, or Subject/Title..." style="padding:10px 14px;">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Start Date</label>
                <input type="date" name="date_from" class="form-control">
            </div>
            <div class="form-group" style="margin:0;">
                <label>End Date</label>
                <input type="date" name="date_to" class="form-control">
            </div>
            <button type="submit" class="btn-red" style="height:40px;"><i class="fas fa-search"></i> Search</button>
        </form>
    </div>

    
    <!-- Document Section -->
    <div style="margin:16px 0; border-top:2px solid #e5e7eb; position:relative;">
        <div style="position:absolute; top:-12px; left:50%; transform:translateX(-50%); background:#f4f6f9; padding:0 20px; color:#666; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:1px;">Document Section</div>
    </div>

    <!-- Document Stat Cards (clickable) -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px,1fr)); gap:16px; margin-bottom:24px;">
        <a href="{{ route('documents.index') }}" style="text-decoration:none;">
            <div class="stat-card yellow" style="cursor:pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="info">
                    <div class="label">Documents</div>
                    <div class="count">{{ $totalDocuments }}</div>
                </div>
                <div class="icon"><i class="fas fa-file-alt"></i></div>
            </div>
        </a>
        <a href="{{ route('documents.index', ['direction' => 'INCOMING']) }}" style="text-decoration:none;">
            <div class="stat-card blue" style="cursor:pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="info">
                    <div class="label">Incoming</div>
                    <div class="count">{{ $docIncoming }}</div>
                </div>
                <div class="icon"><i class="fas fa-arrow-down"></i></div>
            </div>
        </a>
        <a href="{{ route('documents.index', ['status' => 'ONGOING']) }}" style="text-decoration:none;">
            <div class="stat-card green" style="cursor:pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="info">
                    <div class="label">On going Documents</div>
                    <div class="count">{{ $docOngoing }}</div>
                </div>
                <div class="icon"><i class="fas fa-sync-alt"></i></div>
            </div>
        </a>
        <a href="{{ route('documents.index', ['status' => 'COMPLETED']) }}" style="text-decoration:none;">
            <div class="stat-card teal" style="cursor:pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="info">
                    <div class="label">Completed Documents</div>
                    <div class="count">{{ $docCompleted }}</div>
                </div>
                <div class="icon"><i class="fas fa-check-square"></i></div>
            </div>
        </a>
    </div>

    <!-- Visual Divider -->
    <div style="margin:24px 0; border-top:2px solid #e5e7eb; position:relative;">
        <div style="position:absolute; top:-12px; left:50%; transform:translateX(-50%); background:#f4f6f9; padding:0 20px; color:#666; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:1px;">Financial Section</div>
    </div>

    <!-- Financial Cards (clickable) -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap:16px; margin-bottom:24px;">
        <a href="{{ route('financial.index') }}" style="text-decoration:none;">
            <div class="stat-card orange" style="cursor:pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="info">
                    <div class="label">Financial Records</div>
                    <div class="count">{{ $totalFinancial }}</div>
                </div>
                <div class="icon"><i class="fas fa-coins"></i></div>
            </div>
        </a>
        <a href="{{ route('financial.index', ['status' => 'ACTIVE']) }}" style="text-decoration:none;">
            <div class="stat-card green" style="cursor:pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="info">
                    <div class="label">Active</div>
                    <div class="count">{{ $financialActive }}</div>
                </div>
                <div class="icon"><i class="fas fa-check"></i></div>
            </div>
        </a>
        <a href="{{ route('financial.index', ['status' => 'CANCELLED']) }}" style="text-decoration:none;">
            <div class="stat-card red" style="cursor:pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="info">
                    <div class="label">Cancelled</div>
                    <div class="count">{{ $financialCancelled }}</div>
                </div>
                <div class="icon"><i class="fas fa-ban"></i></div>
            </div>
        </a>
        <a href="{{ route('financial.index', ['status' => 'FINISHED']) }}" style="text-decoration:none;">
            <div class="stat-card teal" style="cursor:pointer; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="info">
                    <div class="label">Finished</div>
                    <div class="count">{{ $financialFinished }}</div>
                </div>
                <div class="icon"><i class="fas fa-flag-checkered"></i></div>
            </div>
        </a>
    </div>

    <!-- Trend Chart -->
    <div class="table-card" style="padding:20px; margin-bottom:20px;">
        <h3 style="font-size:16px; font-weight:700; color:#2d3436; margin-bottom:16px;">Trend</h3>
        <canvas id="monthlyChart" height="80"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['0','1','2','3','4','5','6','7','8','9','10','11','12'],
                datasets: [{
                    label: 'Documents',
                    data: [0, ...@json($chartDocuments)],
                    borderColor: 'rgba(102, 51, 153, 1)',
                    backgroundColor: 'rgba(102, 51, 153, 0.15)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(102, 51, 153, 1)',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 50 } },
                    x: { grid: { display: true, color: 'rgba(0,0,0,0.05)' } }
                }
            }
        });
    </script>
</x-app-layout>
