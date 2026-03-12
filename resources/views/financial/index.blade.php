<x-app-layout>
    <x-slot name="header">
        <h1>Financial Monitoring</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / Financial</div>
    </x-slot>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showNotification({
                    type: 'success',
                    title: 'Success!',
                    message: '{{ session('success') }}',
                    duration: 3000
                });
            });
        </script>
    @endif

    <!-- Page Header with Actions
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="margin:0; color:#c0392b;"></h2>
        <div style="display:flex; gap:8px; align-items:center;">
            <button type="button" onclick="toggleQrScanner()" class="btn-blue" style="padding:8px 12px; font-size:12px;">
                <i class="fas fa-qrcode"></i> QR Scanner
            </button>
        </div>
    </div>-->

    <!-- Search Filter -->
    <div class="filter-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="margin:0;">Search Filter</h3>
            @if(request()->hasAny(['status', 'search']))
                <div style="display:flex; gap:4px; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
                    <span style="color:#666; font-size:15px;">Active Filters:</span>
                    @if(request('status'))
                        <span class="badge" style="background:#1976d2; color:white; padding:1px 5px; border-radius:2px; display:flex; align-items:center; gap:3px; font-size:12px; white-space:nowrap;">
                            {{ request('status') }}
                            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove status filter">×</a>
                        </span>
                    @endif
                    @if(request('search'))
                        <span class="badge" style="background:#1976d2; color:white; padding:1px 5px; border-radius:2px; display:flex; align-items:center; gap:3px; font-size:12px; white-space:nowrap;">
                            {{ request('search') }}
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove search filter">×</a>
                        </span>
                    @endif
                </div>
            @endif
        </div>
        
        <form method="GET" action="{{ route('financial.index') }}">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <div style="display:grid; grid-template-columns: 1fr; gap:8px;">
                <div class="form-group" style="margin:0">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Enter keywords...">
                </div>
            </div>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:12px;">
                <div class="form-group" style="margin:0; margin-top:12px;">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                        <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                        <option value="FINISHED" {{ request('status') === 'FINISHED' ? 'selected' : '' }}>Finished</option>
                    </select>
                </div>
                <div class="form-group" style="margin:0; margin-top:12px; display:flex; align-items:flex-end; gap:8px;">
                    <button type="submit" class="btn-red"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('financial.index') }}" class="btn-gray">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Financial Grid - Card Layout -->
    <div class="table-card">
        <div class="table-header" style="display: flex; justify-content: flex-end; align-items: center;">
            <a href="{{ route('financial.create') }}" class="btn-red"><i class="fas fa-plus"></i> Add New Record</a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 20px; padding: 10px 0;">
            @forelse($records as $index => $rec)
                <div class="financial-card" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 1px solid rgba(192,57,43,0.1); border-radius: 16px; padding: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); transition: all 0.3s ease; position: relative; overflow: hidden; cursor: pointer;" onclick="window.location.href='{{ route('financial.show', $rec) }}'">
                    <!-- Card Header -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <span style="background: linear-gradient(135deg, #2980b9 0%, #64b5f6 100%); color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                    #{{ $records->firstItem() + $index }}
                                </span>
                                <span class="badge badge-{{ $rec->status == 'ACTIVE' ? 'ongoing' : ($rec->status == 'CANCELLED' ? 'cancelled' : 'completed') }}" style="font-size: 11px; padding: 4px 8px;">{{ $rec->status }}</span>
                            </div>
                            <h3 style="margin: 0; color: #1a1a2e; font-size: 16px; font-weight: 600; line-height: 1.4;">{{ $rec->description ?? 'No Description' }}</h3>
                        </div>
                        <div style="display: flex; gap: 6px;" onclick="event.stopPropagation();">
                            <a href="{{ route('financial.edit', $rec) }}" class="btn-blue" title="Edit" style="padding: 6px 8px; min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('financial.destroy', $rec) }}" method="POST" style="display: inline;" id="deleteForm-{{ $rec->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-danger" title="Delete" onclick="confirmDelete({{ $rec->id }}, '{{ $rec->description ?? 'Financial Record' }}')" style="padding: 6px 8px; min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Financial Details -->
                    <div style="display: grid; gap: 12px; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-tag" style="color: #2980b9; width: 16px; text-align: center;"></i>
                            <span style="color: #64748b; font-size: 12px; font-weight: 500;">TYPE:</span>
                            <span style="color: #1a1a2e; font-size: 13px; font-weight: 600;">{{ $rec->type ?? '—' }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-building" style="color: #2980b9; width: 16px; text-align: center;"></i>
                            <span style="color: #64748b; font-size: 12px; font-weight: 500;">SUPPLIER:</span>
                            <span style="color: #1a1a2e; font-size: 13px; font-weight: 600;">{{ $rec->supplier ?? '—' }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-file-invoice" style="color: #2980b9; width: 16px; text-align: center;"></i>
                            <span style="color: #64748b; font-size: 12px; font-weight: 500;">PR NO:</span>
                            <span style="color: #1a1a2e; font-size: 13px; font-weight: 600;">{{ $rec->pr_number ?? '—' }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-money-bill-wave" style="color: #2980b9; width: 16px; text-align: center;"></i>
                            <span style="color: #64748b; font-size: 12px; font-weight: 500;">PR AMT:</span>
                            <span style="color: #1a1a2e; font-size: 13px; font-weight: 600;">{{ $rec->pr_amount ? number_format($rec->pr_amount, 2) : '—' }}</span>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-receipt" style="color: #27ae60; width: 16px; text-align: center;"></i>
                            <span style="color: #64748b; font-size: 12px; font-weight: 500;">PO NO:</span>
                            <span style="color: #1a1a2e; font-size: 13px; font-weight: 600;">{{ $rec->po_number ?? '—' }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-file-contract" style="color: #27ae60; width: 16px; text-align: center;"></i>
                            <span style="color: #64748b; font-size: 12px; font-weight: 500;">OBR NO:</span>
                            <span style="color: #1a1a2e; font-size: 13px; font-weight: 600;">{{ $rec->obr_number ?? '—' }}</span>
                        </div>
                    </div>

                    <!-- Progress Section -->
                    <div style="background: rgba(41,128,185,0.05); border-left: 3px solid #2980b9; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <i class="fas fa-chart-line" style="color: #2980b9; font-size: 12px;"></i>
                            <span style="color: #2980b9; font-size: 12px; font-weight: 600;">PROGRESS</span>
                        </div>
                        <p style="margin: 0; color: #475569; font-size: 13px; line-height: 1.4;">{{ $rec->progress ?? 'No Progress Info' }}</p>
                    </div>

                    <!-- Footer -->
                    <div style="display: flex; justify-content: flex-start; align-items: center; padding-top: 12px; border-top: 1px solid rgba(192,57,43,0.1);">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <i class="fas fa-home" style="color: #64748b; font-size: 12px;"></i>
                            <span style="color: #64748b; font-size: 12px;">Office: {{ $rec->originOffice->code ?? '—' }}</span>
                        </div>
                    </div>

                    <!-- Hover Effect Overlay -->
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(192,57,43,0.05) 0%, rgba(41,128,185,0.05) 100%); opacity: 0; transition: opacity 0.3s ease; pointer-events: none; border-radius: 16px;"></div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                    <div style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 2px dashed rgba(192,57,43,0.2); border-radius: 16px; padding: 40px;">
                        <i class="fas fa-coins" style="font-size: 48px; color: #2980b9; margin-bottom: 16px;"></i>
                        <h3 style="color: #1a1a2e; margin-bottom: 8px;">No Financial Records Found</h3>
                        <p style="color: #64748b; margin-bottom: 20px;">Start by adding your first financial record to the system.</p>
                        <a href="{{ route('financial.create') }}" class="btn-red">
                            <i class="fas fa-plus"></i> Add New Record
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        <div style="padding:16px 20px; display:flex; justify-content:center; align-items:center; gap:16px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                @if($records->onFirstPage())
                    <span style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#d1d5db; font-size:13px; font-weight:500; cursor:not-allowed;">
                        <i class="fas fa-chevron-left"></i> Previous
                    </span>
                @else
                    <a href="{{ $records->previousPageUrl() }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                @endif
                
                <div style="display:flex; gap:4px;">
                    @for($i = 1; $i <= min(3, $records->lastPage()); $i++)
                        @if($records->currentPage() == $i)
                            <span style="padding:8px 12px; background:linear-gradient(135deg, #c0392b 0%, #8b0000 100%); border:none; border-radius:6px; color:#ffffff; font-size:13px; font-weight:600; cursor:pointer;">{{ $i }}</span>
                        @else
                            <a href="{{ $records->url($i) }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">{{ $i }}</a>
                        @endif
                    @endfor
                </div>
                
                @if($records->hasMorePages())
                    <a href="{{ $records->nextPageUrl() }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">
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

    <!-- QR Scanner Modal -->
     <div id="qrScannerModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
        <div style="background:white; border-radius:8px; padding:20px; max-width:500px; width:90%; max-height:80vh; overflow-y:auto;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <h3 style="margin:0; color:#c0392b;">QR Code Scanner</h3>
                <button onclick="toggleQrScanner()" style="background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
            </div>

            <!-- Scan Panel Only -->
            <div>
                <div style="border:1px solid #ddd; border-radius:4px; padding:20px; text-align:center; min-height:150px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                        <span style="font-size:14px; font-weight:600; color:#c0392b;">Camera Scanner</span>
                        <span id="qrScanStatus" style="font-size:12px; color:#999;">IDLE</span>
                    </div>
                    
                    <!-- Camera View -->
                    <div id="qrCameraContainer" style="display:none; position:relative; margin-bottom:12px;">
                        <video id="qrVideo" style="width:100%; max-width:320px; height:240px; border-radius:4px; background:#000;"></video>
                        <canvas id="qrCanvas" style="display:none;"></canvas>
                        <button type="button" onclick="stopQrCamera()" style="position:absolute; top:8px; right:8px; background:rgba(0,0,0,0.7); color:#fff; border:none; width:24px; height:24px; border-radius:50%; font-size:12px; cursor:pointer;">&times;</button>
                    </div> 

                    <!-- File Upload -->
                    <div id="qrUploadContainer" style="display:none; margin-bottom:12px;">
                        <input type="file" id="qrFileInput" accept="image/*" style="display:none;" onchange="handleQrFileUpload(event)">
                        <button type="button" onclick="document.getElementById('qrFileInput').click()" style="padding:8px 16px; background:#2980b9; color:#fff; border:none; border-radius:4px; font-size:13px; font-weight:600; cursor:pointer;">
                            <i class="fas fa-upload"></i> Choose Image
                        </button>
                        <div id="qrUploadPreview" style="margin-top:8px;"></div>
                    </div>

                    <div style="padding:20px 0;">
                        <button type="button" id="qrStartCameraBtn" onclick="startQrCamera()" style="padding:10px 24px; background:#c0392b; color:#fff; border:none; border-radius:4px; font-size:13px; font-weight:600; cursor:pointer;">
                            <i class="fas fa-camera"></i> Start Camera
                        </button>
                        <div style="margin-top:10px;">
                            <button type="button" onclick="showQrFileUpload()" style="background:none; border:none; color:#2980b9; font-size:12px; text-decoration:underline; cursor:pointer;">
                                <i class="fas fa-image"></i> Scan an Image File
                            </button>
                        </div>
                    </div>
                </div>
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

        .table-header h3 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .filter-box {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
        }

        .filter-box h3 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .form-group label {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-control {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
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

        .badge-active { background: #27ae60; color: #fff; }
        .badge-cancelled { background: #e74c3c; color: #fff; }
        .badge-finished { background: #00b894; color: #fff; }
        .badge-warning { background: #f39c12; color: #fff; }
        .badge-info { background: #3498db; color: #fff; }
        .badge-primary { background: #9b59b6; color: #fff; }
        .badge-success { background: #27ae60; color: #fff; }
        .badge-completed { background: #16a085; color: #fff; }

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
        // QR Scanner Variables
        let qrCodeReader = null;
        let qrCurrentStream = null;

        function toggleQrScanner() {
            const modal = document.getElementById('qrScannerModal');
            
            if (modal.style.display === 'none' || modal.style.display === '') {
                modal.style.display = 'flex';
                resetQrScanner();
            } else {
                modal.style.display = 'none';
                stopQrCamera();
            }
        }

        function showQrTab(tab) {
            document.getElementById('qrPanelSearch').style.display = tab === 'search' ? 'block' : 'none';
            document.getElementById('qrPanelScan').style.display = tab === 'scan' ? 'block' : 'none';
            document.getElementById('qrTabSearch').style.background = tab === 'search' ? '#c0392b' : '#e9ecef';
            document.getElementById('qrTabSearch').style.color = tab === 'search' ? '#fff' : '#555';
            document.getElementById('qrTabScan').style.background = tab === 'scan' ? '#c0392b' : '#e9ecef';
            document.getElementById('qrTabScan').style.color = tab === 'scan' ? '#fff' : '#555';
        }

        function searchQrCode() {
            const code = document.getElementById('qrSearchInput').value.trim();
            if (!code) {
                alert('Please enter a tracking code.');
                return;
            }

            // Set the search value and submit the form
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.value = code;
                document.querySelector('form').submit();
            }
        }

        function startQrCamera() {
            if (!qrCodeReader) {
                qrCodeReader = new ZXing.BrowserQRCodeReader();
            }

            const videoElement = document.getElementById('qrVideo');
            const startBtn = document.getElementById('qrStartCameraBtn');
            const cameraContainer = document.getElementById('qrCameraContainer');
            const status = document.getElementById('qrScanStatus');

            startBtn.disabled = true;
            startBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Starting...';
            status.textContent = 'STARTING';
            status.style.color = '#f39c12';

            qrCodeReader.decodeFromVideoDevice(null, videoElement, (result, err) => {
                if (result) {
                    document.getElementById('qrSearchInput').value = result.text;
                    showQrTab('search');
                    stopQrCamera();
                    searchQrCode();
                }
            }).then(() => {
                cameraContainer.style.display = 'block';
                startBtn.style.display = 'none';
                status.textContent = 'SCANNING';
                status.style.color = '#27ae60';
            }).catch((err) => {
                console.error('Camera error:', err);
                status.textContent = 'ERROR';
                status.style.color = '#e74c3c';
                startBtn.disabled = false;
                startBtn.innerHTML = '<i class="fas fa-camera"></i> Start Camera';
                alert('Camera access denied or not available. Please use the file upload option or enter the code manually.');
            });
        }

        function stopQrCamera() {
            if (qrCodeReader && qrCurrentStream) {
                qrCodeReader.reset();
                qrCurrentStream = null;
            }
            document.getElementById('qrCameraContainer').style.display = 'none';
            document.getElementById('qrStartCameraBtn').style.display = 'inline-block';
            document.getElementById('qrStartCameraBtn').disabled = false;
            document.getElementById('qrStartCameraBtn').innerHTML = '<i class="fas fa-camera"></i> Start Camera';
            document.getElementById('qrScanStatus').textContent = 'IDLE';
            document.getElementById('qrScanStatus').style.color = '#999';
        }

        function showQrFileUpload() {
            document.getElementById('qrUploadContainer').style.display = 'block';
        }

        function handleQrFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const preview = document.getElementById('qrUploadPreview');
            preview.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing image...';

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0);

                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    
                    if (!qrCodeReader) {
                        qrCodeReader = new ZXing.BrowserQRCodeReader();
                    }

                    try {
                        const result = qrCodeReader.decodeFromImageData(imageData);
                        if (result) {
                            document.getElementById('qrSearchInput').value = result.text;
                            showQrTab('search');
                            preview.innerHTML = '<div style="color:#27ae60; font-size:12px;"><i class="fas fa-check-circle"></i> QR Code detected: ' + result.text + '</div>';
                            setTimeout(() => {
                                searchQrCode();
                                document.getElementById('qrUploadContainer').style.display = 'none';
                                preview.innerHTML = '';
                            }, 1000);
                        } else {
                            preview.innerHTML = '<div style="color:#e74c3c; font-size:12px;"><i class="fas fa-exclamation-circle"></i> No QR code found in image</div>';
                        }
                    } catch (err) {
                        preview.innerHTML = '<div style="color:#e74c3c; font-size:12px;"><i class="fas fa-exclamation-circle"></i> Could not read QR code from image</div>';
                    }
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        function resetQrScanner() {
            document.getElementById('qrSearchInput').value = '';
            document.getElementById('qrSearchError').style.display = 'none';
            document.getElementById('qrUploadPreview').innerHTML = '';
            showQrTab('search');
            stopQrCamera();
        }

        document.getElementById('qrSearchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { searchQrCode(); }
        });

        // Close modal when clicking outside
        document.getElementById('qrScannerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                toggleQrScanner();
            }
        });

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

        function confirmDelete(recordId, description) {
            console.log('confirmDelete called with:', recordId, description); // Debug log
            
            showConfirmDialog({
                title: 'Delete Financial Record',
                message: `Are you sure you want to delete this financial record?<br><br><strong>Description:</strong> ${description}<br><strong>This action cannot be undone!</strong>`,
                confirmText: 'Delete',
                cancelText: 'Cancel',
                confirmClass: 'notification-btn-confirm',
                onConfirm: function() {
                    console.log('Delete confirmed, submitting form:', recordId); // Debug log
                    const form = document.getElementById(`deleteForm-${recordId}`);
                    if (form) {
                        form.submit();
                    } else {
                        console.error('Form not found:', `deleteForm-${recordId}`);
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
            console.log('Financial page loaded, notification system ready');
            
            // Uncomment to test automatically:
            // setTimeout(testNotification, 1000);
        });
    </script>
</x-app-layout>
