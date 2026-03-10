<x-app-layout>
    <x-slot name="header">
        <h1>Documents</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / Documents</div>
    </x-slot>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Use the global notification system from app layout
                if (typeof window.showNotification === 'function') {
                    window.showNotification({
                        type: 'success',
                        title: 'Success!',
                        message: '{{ session('success') }}',
                        duration: 3000
                    });
                } else {
                    console.error('Global showNotification function not found');
                }
            });
        </script>
    @endif

    <!-- Page Header with Actions -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="margin:0; color:#c0392b;">Documents Management</h2>
        <div style="display:flex; gap:8px; align-items:center;">
            <button type="button" onclick="toggleQrScanner()" class="btn-blue" style="padding:8px 12px; font-size:12px;">
                <i class="fas fa-qrcode"></i> QR Scanner
            </button>
        </div>
    </div>

    <!-- Search Filter -->
    <div class="filter-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="margin:0;">Search Filter</h3>
            @if(request()->hasAny(['direction', 'status', 'type', 'month', 'year', 'search']))
                <div style="display:flex; gap:4px; align-items:center; flex-wrap:wrap; justify-content:flex-end; margin-bottom:12px;">
                    <span style="color:#666; font-size:15px;">Active Filters:</span>
                    @if(request('direction'))
                        <span class="badge" style="background:#1976d2; color:white; padding:1px 5px; border-radius:2px; display:flex; align-items:center; gap:3px; font-size:12px; white-space:nowrap;">
                            {{ request('direction') }}
                            <a href="{{ request()->fullUrlWithQuery(['direction' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove direction filter">×</a>
                        </span>
                    @endif
                    @if(request('status'))
                        <span class="badge" style="background:#1976d2; color:white; padding:1px 5px; border-radius:2px; display:flex; align-items:center; gap:3px; font-size:12px; white-space:nowrap;">
                            {{ request('status') }}
                            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove status filter">×</a>
                        </span>
                    @endif
                    @if(request('type'))
                        <span class="badge" style="background:#1976d2; color:white; padding:1px 5px; border-radius:2px; display:flex; align-items:center; gap:3px; font-size:12px; white-space:nowrap;">
                            {{ request('type') }}
                            <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove type filter">×</a>
                        </span>
                    @endif
                    @if(request('month'))
                        <span class="badge" style="background:#1976d2; color:white; padding:1px 5px; border-radius:2px; display:flex; align-items:center; gap:3px; font-size:12px; white-space:nowrap;">
                            {{ request('month') }}
                            <a href="{{ request()->fullUrlWithQuery(['month' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove month filter">×</a>
                        </span>
                    @endif
                    @if(request('year'))
                        <span class="badge" style="background:#1976d2; color:white; padding:1px 5px; border-radius:2px; display:flex; align-items:center; gap:3px; font-size:12px; white-space:nowrap;">
                            {{ request('year') }}
                            <a href="{{ request()->fullUrlWithQuery(['year' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove year filter">×</a>
                        </span>
                    @endif
                    @if(request('search'))
                        <span class="badge" style="background:#1976d2; color:white; padding:1px 5px; border-radius:2px; display:flex; align-items:center; gap:3px; font-size:12px; white-space:nowrap;">
                            {{ request('search') }}
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove search filter">×</a>
                        </span>
                    @endif
                    <a href="{{ route('documents.index') }}" class="btn btn-sm btn-outline-secondary">Clear All</a>
                </div>
            @endif
        </div>
        <form method="GET" action="{{ route('documents.index') }}">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if(request('direction'))
                <input type="hidden" name="direction" value="{{ request('direction') }}">
            @endif
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            @if(request('month'))
                <input type="hidden" name="month" value="{{ request('month') }}">
            @endif
            @if(request('year'))
                <input type="hidden" name="year" value="{{ request('year') }}">
            @endif
            <div style="display:grid; grid-template-columns: 1fr; gap:8px;">
                <div class="form-group" style="margin:0">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Enter keywords...">
                </div>
            </div>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:12px;">
                <div class="form-group" style="margin:0; margin-top:12px;">
                    <label>Month</label>
                    <select name="month" class="form-control">
                        <option value="">All Months</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group" style="margin:0; margin-top:12px;">
                    <label>Year</label>
                    <input type="number" name="year" class="form-control" value="{{ request('year', now()->year) }}" min="2020" max="2030">
                </div>
                <div class="form-group" style="margin:0; margin-top:12px;">
                    <label>Document Type</label>
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        @foreach(['MEMO','EO','SO','LETTER','SP','OTHERS'] as $t)
                            <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin:0; margin-top:12px;">
                    <label>Direction</label>
                    <select name="direction" class="form-control">
                        <option value="">All</option>
                        <option value="INCOMING" {{ request('direction') === 'INCOMING' ? 'selected' : '' }}>Incoming</option>
                        <option value="OUTGOING" {{ request('direction') === 'OUTGOING' ? 'selected' : '' }}>Outgoing</option>
                    </select>
                </div>
                <div class="form-group" style="margin:0; margin-top:12px; display:flex; align-items:flex-end; gap:8px;">
                    <button type="submit" class="btn-red"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('documents.index') }}" class="btn-gray">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Documents Table -->
    <div class="table-card">
        <div class="table-header">
            <h3>Documents Table</h3>
            <div style="display:flex; gap:8px;">
                <a href="{{ route('documents.create') }}" class="btn-red"><i class="fas fa-plus"></i> Add New Document</a>
            </div>
        </div>

        <div style="overflow-x:auto; max-width:100%;">
            <table style="min-width:900px; width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:60px; border-bottom:2px solid #8b0000;">SEQ #</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">ACTION</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:150px; border-bottom:2px solid #8b0000;">TRACKING CODE</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:180px; border-bottom:2px solid #8b0000;">PICTO NO</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:150px; border-bottom:2px solid #8b0000;">NUMBER</th>
                        <th style="text-align:center; padding:12px 8px; min-width:250px; border-bottom:2px solid #8b0000;">SUBJECT</th>
                        <th style="text-align:center; padding:12px 8px; min-width:200px; border-bottom:2px solid #8b0000;">REMARKS</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:100px; border-bottom:2px solid #8b0000;">STATUS</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">DOCUMENT DATE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $index => $doc)
                        <tr class="clickable-row" data-href="{{ route('documents.show', $doc) }}" style="cursor: pointer;">
                            <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:60px;">{{ $documents->firstItem() + $index }}</td>
                            <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;" onclick="event.stopPropagation();">
                                <div style="display:flex; gap:4px; align-items:center; justify-content:flex-start;">
                                    <a href="{{ route('documents.edit', $doc) }}" class="btn-blue" title="Edit" style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('documents.destroy', $doc) }}" method="POST" style="display:inline;" id="deleteForm-{{ $doc->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn-danger" title="Delete" onclick="confirmDelete({{ $doc->id }}, '{{ $doc->dts_number }}')" style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                            <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:150px;">{{ $doc->dts_number }}</td>
                            <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:180px;">{{ $doc->doc_number ?? '—' }}</td>
                            <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:150px;">{{ $doc->memorandum_number ?? '—' }}</td>
                            <td style="text-align:left; padding:20px 20px 20px 20px; min-width:250px; word-wrap:break-word;">{{ $doc->subject }}</td>
                            <td style="text-align:left; padding:20px 20px 20px 20px; min-width:200px; max-width:250px; word-wrap:break-word;">{{ $doc->remarks ?? '—' }}</td>
                            <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:100px;">
                                @php
                                    $badgeClass = match($doc->status) {
                                        'ONGOING' => 'badge-ongoing',
                                        'DELIVERED' => 'badge-delivered',
                                        'COMPLETED' => 'badge-completed',
                                        default => ''
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $doc->status }}</span>
                            </td>
                            <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;">{{ $doc->date_received ? $doc->date_received->format('F d, Y') : ($doc->created_at ? $doc->created_at->format('F d, Y') : '—') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding:30px; color:#999;">No documents found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding:16px 20px; display:flex; justify-content:center;">
            {{ $documents->links() }}
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

        .notification-btn-success {
            background: #27ae60;
            color: white;
        }

        .notification-btn-success:hover {
            background: #229954;
        }

        .notification.removing {
            animation: slideOutRight 0.3s ease-out forwards;
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
                    // Set the search value and submit the form
                    const searchInput = document.querySelector('input[name="search"]');
                    if (searchInput) {
                        searchInput.value = result.text;
                        stopQrCamera();
                        toggleQrScanner();
                        document.querySelector('form').submit();
                    }
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
                alert('Camera access denied or not available. Please use the file upload option.');
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
                            preview.innerHTML = '<div style="color:#27ae60; font-size:12px;"><i class="fas fa-check-circle"></i> QR Code detected: ' + result.text + '</div>';
                            setTimeout(() => {
                                // Set the search value and submit the form
                                const searchInput = document.querySelector('input[name="search"]');
                                if (searchInput) {
                                    searchInput.value = result.text;
                                    toggleQrScanner();
                                    document.querySelector('form').submit();
                                }
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
            document.getElementById('qrUploadPreview').innerHTML = '';
            stopQrCamera();
        }

        // Close modal when clicking outside
        document.getElementById('qrScannerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                toggleQrScanner();
            }
        });

        // Existing notification system - Local implementation
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
                    case 'warning':
                        iconHtml = '<i class="fas fa-exclamation-triangle"></i>';
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
                    <button class="notification-close" onclick="window.removeNotification(this)">&times;</button>
                </div>
                <div class="notification-message">${message}</div>
                ${actionsHtml}
            `;

            container.appendChild(notification);

            // Auto-remove after duration
            if (duration > 0) {
                setTimeout(() => {
                    window.removeNotification(notification.querySelector('.notification-close'));
                }, duration);
            }

            return notification;
        }

        window.removeNotification = function(element) {
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

        // Show completed notification
        window.showCompletedNotification = function(title, message) {
            showNotification({
                type: 'success',
                title: title,
                message: message,
                duration: 3000,
                icon: 'fas fa-check-circle'
            });
        }

        // Confirmation dialog function
        window.showConfirmDialog = function(options) {
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
                const notification = window.showNotification({
                    type: 'warning',
                    title: title,
                    message: message,
                    duration: 0, // Don't auto-close
                    actions: [
                        {
                            text: cancelText,
                            class: 'notification-btn-cancel',
                            onclick: `window.removeNotification(this.closest('.notification').querySelector('.notification-close')); window.confirmDialogCancel();`
                        },
                        {
                            text: confirmText,
                            class: confirmClass,
                            onclick: `window.removeNotification(this.closest('.notification').querySelector('.notification-close')); window.confirmDialogConfirm();`
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

        function confirmDelete(docId, trackingCode) {
            console.log('confirmDelete called with:', docId, trackingCode); // Debug log
            
            showConfirmDialog({
                title: 'Delete Document',
                message: `Are you sure you want to delete this document?<br><br><strong>Tracking Code:</strong> ${trackingCode}<br><strong>This action cannot be undone!</strong>`,
                confirmText: 'Delete',
                cancelText: 'Cancel',
                confirmClass: 'notification-btn-confirm',
                onConfirm: function() {
                    console.log('Delete confirmed, submitting form:', docId); // Debug log
                    const form = document.getElementById(`deleteForm-${docId}`);
                    if (form) {
                        form.submit();
                    } else {
                        console.error('Form not found:', `deleteForm-${docId}`);
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
            console.log('Document loaded, notification system ready');
            
            // Clickable table rows
            const clickableRows = document.querySelectorAll('.clickable-row');
            clickableRows.forEach(row => {
                row.addEventListener('click', function() {
                    const href = this.getAttribute('data-href');
                    if (href) {
                        window.location.href = href;
                    }
                });
            });
            
            // Uncomment to test automatically:
            // setTimeout(testNotification, 1000);
        });
    </script>
</x-app-layout>
