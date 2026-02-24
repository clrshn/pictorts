<x-app-layout>
    <x-slot name="header">
        <h1>Track Document</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / Track Document</div>
    </x-slot>

    <div class="filter-box" style="max-width:600px;">
        <h3><i class="fas fa-qrcode"></i> Track Document</h3>

        <!-- Instruction -->
        <div style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:4px; padding:10px 14px; font-size:12px; color:#555; margin-bottom:16px;">
            Use the QR Code scanner and scan image file under Scan Tab or just enter the QR Code under Search Tab to <strong>track</strong> document
        </div>

        <!-- Search / Scan Tabs -->
        <div style="display:flex; gap:0; margin-bottom:16px;">
            <button id="sTabSearch" onclick="showSysTrackTab('search')" style="padding:10px 28px; font-size:13px; font-weight:700; border:none; cursor:pointer; border-radius:4px 0 0 4px; background:#c0392b; color:#fff;">Search</button>
            <button id="sTabScan" onclick="showSysTrackTab('scan')" style="padding:10px 28px; font-size:13px; font-weight:700; border:none; cursor:pointer; border-radius:0 4px 4px 0; background:#e9ecef; color:#555;">Scan</button>
        </div>

        <!-- Search Panel -->
        <div id="sPanelSearch">
            <div style="position:relative; margin-bottom:16px;">
                <input type="text" id="sTrackCodeInput" placeholder="QR Code / Tracking Number / DTS Number" class="form-control" style="padding-right:40px;">
                <i class="fas fa-qrcode" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#c0392b; font-size:16px;"></i>
            </div>
            <button type="button" onclick="sysTrackDocument()" id="sTrackBtn" class="btn-red" style="width:100%; justify-content:center; padding:12px; border-radius:30px; font-size:14px; letter-spacing:1px;">
                TRACK &nbsp;<i class="fas fa-arrow-right"></i>
            </button>
            <div id="sTrackError" style="display:none; margin-top:12px; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; padding:10px 14px; border-radius:4px; font-size:13px;"></div>
            <div id="sTrackLoading" style="display:none; margin-top:12px; text-align:center; color:#999; font-size:13px;">
                <i class="fas fa-spinner fa-spin"></i> Searching...
            </div>
        </div>

        <!-- Scan Panel -->
        <div id="sPanelScan" style="display:none;">
            <div style="border:1px solid #ddd; border-radius:4px; padding:20px; text-align:center; min-height:150px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                    <span style="font-size:14px; font-weight:600; color:#c0392b;">Code Scanner</span>
                    <span id="sScanStatus" style="font-size:12px; color:#999;">IDLE</span>
                </div>
                
                <!-- Camera View -->
                <div id="sCameraContainer" style="display:none; position:relative; margin-bottom:12px;">
                    <video id="sVideo" style="width:100%; max-width:320px; height:240px; border-radius:4px; background:#000;"></video>
                    <canvas id="sCanvas" style="display:none;"></canvas>
                    <button type="button" onclick="stopSysCamera()" style="position:absolute; top:8px; right:8px; background:rgba(0,0,0,0.7); color:#fff; border:none; width:24px; height:24px; border-radius:50%; font-size:12px; cursor:pointer;">&times;</button>
                </div>

                <!-- File Upload -->
                <div id="sUploadContainer" style="display:none; margin-bottom:12px;">
                    <input type="file" id="sFileInput" accept="image/*" style="display:none;" onchange="handleSysFileUpload(event)">
                    <button type="button" onclick="document.getElementById('sFileInput').click()" style="padding:8px 16px; background:#2980b9; color:#fff; border:none; border-radius:4px; font-size:13px; font-weight:600; cursor:pointer;">
                        <i class="fas fa-upload"></i> Choose Image
                    </button>
                    <div id="sUploadPreview" style="margin-top:8px;"></div>
                </div>

                <div style="padding:20px 0;">
                    <button type="button" id="sStartCameraBtn" onclick="startSysCamera()" style="padding:10px 24px; background:#c0392b; color:#fff; border:none; border-radius:4px; font-size:13px; font-weight:600; cursor:pointer;">
                        <i class="fas fa-camera"></i> Start Camera
                    </button>
                    <div style="margin-top:10px;">
                        <button type="button" onclick="showSysFileUpload()" style="background:none; border:none; color:#2980b9; font-size:12px; text-decoration:underline; cursor:pointer;">
                            <i class="fas fa-image"></i> Scan an Image File
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Track Result (inline, not modal) -->
    <div id="sTrackResult" style="display:none; margin-top:20px;">
        <div class="table-card">
            <!-- Header -->
            <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="font-size:18px; font-weight:700; color:#2d3436; margin:0;">Document Track</h3>
                <button onclick="document.getElementById('sTrackResult').style.display='none'" style="background:#c0392b; color:#fff; border:none; width:28px; height:28px; border-radius:4px; font-size:14px; cursor:pointer; font-weight:700;">&times;</button>
            </div>

            <div style="padding:20px 24px;">
                <h4 style="text-align:center; color:#c0392b; font-size:16px; font-weight:700; margin-bottom:16px;">This document is confidential!</h4>

                <!-- Document Details Grid -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px 24px; font-size:13px; margin-bottom:24px;">
                    <div><span style="color:#888;">Tracking Number:</span> <strong id="sResTrackingNo"></strong></div>
                    <div><span style="color:#888;">Direction:</span> <strong id="sResDirection"></strong></div>
                    <div><span style="color:#888;">Document Type:</span> <strong id="sResDocType"></strong></div>
                    <div><span style="color:#888;">ICTU Number:</span> <strong id="sResIctu"></strong></div>
                    <div><span style="color:#888;">Originating Office:</span> <strong id="sResOrigin"></strong></div>
                    <div><span style="color:#888;">Action Required:</span> <strong id="sResAction"></strong></div>
                    <div><span style="color:#888;">Subject:</span> <strong id="sResSubject"></strong></div>
                    <div><span style="color:#888;">Endorsed To:</span> <strong id="sResEndorsed"></strong></div>
                    <div><span style="color:#888;">Remarks:</span> <strong id="sResRemarks"></strong></div>
                    <div><span style="color:#888;">Date:</span> <strong id="sResDate"></strong></div>
                    <div><span style="color:#888;">Date Received:</span> <strong id="sResDateReceived"></strong></div>
                    <div><span style="color:#888;">Current Location:</span> <strong id="sResLocation"></strong></div>
                    <div><span style="color:#888;">Current Holder:</span> <strong id="sResHolder"></strong></div>
                    <div><span style="color:#888;">Status:</span> <strong id="sResStatus"></strong></div>
                </div>

                <!-- Tracks Section -->
                <h4 style="font-size:16px; font-weight:700; color:#2d3436; margin-bottom:12px;">Tracks</h4>

                <!-- Timeline / Table Tabs -->
                <div style="display:flex; gap:0; margin-bottom:12px; border-bottom:2px solid #e5e7eb;">
                    <button id="sTabTimeline" onclick="showSysTracksTab('timeline')" style="padding:8px 20px; font-size:13px; font-weight:600; border:none; cursor:pointer; background:transparent; color:#c0392b; border-bottom:2px solid #c0392b; margin-bottom:-2px;">Timeline Tracking</button>
                    <button id="sTabTable" onclick="showSysTracksTab('table')" style="padding:8px 20px; font-size:13px; font-weight:600; border:none; cursor:pointer; background:transparent; color:#888; border-bottom:2px solid transparent; margin-bottom:-2px;">Table Tracking</button>
                </div>

                <!-- Timeline View -->
                <div id="sPanelTimeline">
                    <div id="sTimelineContent" style="background:#f4f6f9; border-radius:6px; padding:20px; min-height:80px;"></div>
                </div>

                <!-- Table View -->
                <div id="sPanelTable" style="display:none;">
                    <table style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr style="background:#3498db;">
                                <th style="padding:10px 14px; color:#fff; font-size:12px; font-weight:600; text-align:left;">DATE</th>
                                <th style="padding:10px 14px; color:#fff; font-size:12px; font-weight:600; text-align:left;">TIME</th>
                                <th style="padding:10px 14px; color:#fff; font-size:12px; font-weight:600; text-align:left;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="sTableTrackBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div style="padding:12px 24px; text-align:right; border-top:1px solid #e5e7eb;">
                <button onclick="document.getElementById('sTrackResult').style.display='none'" style="padding:8px 24px; background:#c0392b; color:#fff; border:none; border-radius:4px; font-size:13px; font-weight:600; cursor:pointer;">Close</button>
            </div>
        </div>
    </div>

    <script>
        function showSysTrackTab(tab) {
            document.getElementById('sPanelSearch').style.display = tab === 'search' ? 'block' : 'none';
            document.getElementById('sPanelScan').style.display = tab === 'scan' ? 'block' : 'none';
            document.getElementById('sTabSearch').style.background = tab === 'search' ? '#c0392b' : '#e9ecef';
            document.getElementById('sTabSearch').style.color = tab === 'search' ? '#fff' : '#555';
            document.getElementById('sTabScan').style.background = tab === 'scan' ? '#c0392b' : '#e9ecef';
            document.getElementById('sTabScan').style.color = tab === 'scan' ? '#fff' : '#555';
        }

        function showSysTracksTab(tab) {
            document.getElementById('sPanelTimeline').style.display = tab === 'timeline' ? 'block' : 'none';
            document.getElementById('sPanelTable').style.display = tab === 'table' ? 'block' : 'none';
            document.getElementById('sTabTimeline').style.color = tab === 'timeline' ? '#c0392b' : '#888';
            document.getElementById('sTabTimeline').style.borderBottom = tab === 'timeline' ? '2px solid #c0392b' : '2px solid transparent';
            document.getElementById('sTabTable').style.color = tab === 'table' ? '#c0392b' : '#888';
            document.getElementById('sTabTable').style.borderBottom = tab === 'table' ? '2px solid #c0392b' : '2px solid transparent';
        }

        function sysTrackDocument() {
            var code = document.getElementById('sTrackCodeInput').value.trim();
            if (!code) { alert('Please enter a tracking code.'); return; }

            document.getElementById('sTrackError').style.display = 'none';
            document.getElementById('sTrackLoading').style.display = 'block';
            document.getElementById('sTrackBtn').disabled = true;
            document.getElementById('sTrackResult').style.display = 'none';

            fetch('{{ route("track.search") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ tracking_code: code })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                document.getElementById('sTrackLoading').style.display = 'none';
                document.getElementById('sTrackBtn').disabled = false;

                if (!data.found) {
                    document.getElementById('sTrackError').textContent = data.message;
                    document.getElementById('sTrackError').style.display = 'block';
                    return;
                }

                var d = data.document;
                document.getElementById('sResTrackingNo').textContent = d.tracking_number;
                document.getElementById('sResDocType').textContent = d.document_type;
                document.getElementById('sResDirection').textContent = d.direction;
                document.getElementById('sResOrigin').textContent = d.originating_office;
                document.getElementById('sResSubject').textContent = d.subject;
                document.getElementById('sResRemarks').textContent = d.remarks;
                document.getElementById('sResDate').textContent = d.date;
                document.getElementById('sResDateReceived').textContent = d.date_received;
                document.getElementById('sResLocation').textContent = d.current_location;
                document.getElementById('sResHolder').textContent = d.current_holder;
                document.getElementById('sResStatus').textContent = d.status;
                document.getElementById('sResIctu').textContent = d.ictu_number;
                document.getElementById('sResAction').textContent = d.action_required;
                document.getElementById('sResEndorsed').textContent = d.endorsed_to;

                // Build timeline
                var routes = data.routes;
                var timelineHtml = '';
                if (routes.length === 0) {
                    timelineHtml = '<div style="text-align:center; color:#999; font-size:13px; padding:20px;">No routing history yet.</div>';
                } else {
                    timelineHtml += '<div style="position:relative; padding-left:30px;">';
                    timelineHtml += '<div style="position:relative; padding-bottom:20px;">';
                    timelineHtml += '<div style="position:absolute; left:-22px; top:2px; width:16px; height:16px; border-radius:50%; background:#27ae60; border:2px solid #fff;"></div>';
                    timelineHtml += '<div style="font-size:12px; font-weight:700; color:#27ae60;">Start</div>';
                    timelineHtml += '</div>';
                    for (var i = 0; i < routes.length; i++) {
                        var r = routes[i];
                        var isLast = (i === routes.length - 1);
                        var dotColor = isLast ? '#c0392b' : '#3498db';
                        timelineHtml += '<div style="position:relative; padding-bottom:20px; border-left:2px solid #ddd; margin-left:-14px; padding-left:22px;">';
                        timelineHtml += '<div style="position:absolute; left:-9px; top:2px; width:16px; height:16px; border-radius:50%; background:' + dotColor + '; border:2px solid #fff;"></div>';
                        timelineHtml += '<div style="font-size:12px; color:#444;"><strong>' + r.date + ' ' + r.time + '</strong></div>';
                        timelineHtml += '<div style="font-size:12px; color:#666;">' + r.action + '</div>';
                        if (r.received_date) {
                            timelineHtml += '<div style="font-size:11px; color:#27ae60;">Received: ' + r.received_date + '</div>';
                        }
                        timelineHtml += '</div>';
                    }
                    if (d.status === 'COMPLETED') {
                        timelineHtml += '<div style="position:relative; border-left:2px solid #ddd; margin-left:-14px; padding-left:22px;">';
                        timelineHtml += '<div style="position:absolute; left:-9px; top:2px; width:16px; height:16px; border-radius:50%; background:#c0392b; border:2px solid #fff;"></div>';
                        timelineHtml += '<div style="font-size:12px; font-weight:700; color:#c0392b;">End</div>';
                        timelineHtml += '</div>';
                    }
                    timelineHtml += '</div>';
                }
                document.getElementById('sTimelineContent').innerHTML = timelineHtml;

                // Build table
                var tableHtml = '';
                for (var j = 0; j < routes.length; j++) {
                    var rt = routes[j];
                    tableHtml += '<tr style="border-bottom:1px solid #f0f0f0;">';
                    tableHtml += '<td style="padding:10px 14px; font-size:12px; color:#444;">' + rt.date + '</td>';
                    tableHtml += '<td style="padding:10px 14px; font-size:12px; color:#444;">' + rt.time + '</td>';
                    tableHtml += '<td style="padding:10px 14px; font-size:12px; color:#444;">' + rt.action + '</td>';
                    tableHtml += '</tr>';
                }
                if (routes.length === 0) {
                    tableHtml = '<tr><td colspan="3" style="padding:20px; text-align:center; color:#999; font-size:13px;">No routing history yet.</td></tr>';
                }
                document.getElementById('sTableTrackBody').innerHTML = tableHtml;

                document.getElementById('sTrackResult').style.display = 'block';
                showSysTracksTab('timeline');
                document.getElementById('sTrackResult').scrollIntoView({ behavior: 'smooth' });
            })
            .catch(function(err) {
                document.getElementById('sTrackLoading').style.display = 'none';
                document.getElementById('sTrackBtn').disabled = false;
                document.getElementById('sTrackError').textContent = 'An error occurred. Please try again.';
                document.getElementById('sTrackError').style.display = 'block';
            });
        }

        // QR Scanner Variables for System Page
        let sysCodeReader = null;
        let sysCurrentStream = null;

        // System Camera Scanner Functions
        function startSysCamera() {
            if (!sysCodeReader) {
                sysCodeReader = new ZXing.BrowserQRCodeReader();
            }

            const videoElement = document.getElementById('sVideo');
            const startBtn = document.getElementById('sStartCameraBtn');
            const cameraContainer = document.getElementById('sCameraContainer');
            const status = document.getElementById('sScanStatus');

            startBtn.disabled = true;
            startBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Starting...';
            status.textContent = 'STARTING';
            status.style.color = '#f39c12';

            sysCodeReader.decodeFromVideoDevice(null, videoElement, (result, err) => {
                if (result) {
                    document.getElementById('sTrackCodeInput').value = result.text;
                    showSysTrackTab('search');
                    stopSysCamera();
                    sysTrackDocument();
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

        function stopSysCamera() {
            if (sysCodeReader && sysCurrentStream) {
                sysCodeReader.reset();
                sysCurrentStream = null;
            }
            document.getElementById('sCameraContainer').style.display = 'none';
            document.getElementById('sStartCameraBtn').style.display = 'inline-block';
            document.getElementById('sStartCameraBtn').disabled = false;
            document.getElementById('sStartCameraBtn').innerHTML = '<i class="fas fa-camera"></i> Start Camera';
            document.getElementById('sScanStatus').textContent = 'IDLE';
            document.getElementById('sScanStatus').style.color = '#999';
        }

        // System File Upload Functions
        function showSysFileUpload() {
            document.getElementById('sUploadContainer').style.display = 'block';
        }

        function handleSysFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const preview = document.getElementById('sUploadPreview');
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
                    
                    if (!sysCodeReader) {
                        sysCodeReader = new ZXing.BrowserQRCodeReader();
                    }

                    try {
                        const result = sysCodeReader.decodeFromImageData(imageData);
                        if (result) {
                            document.getElementById('sTrackCodeInput').value = result.text;
                            showSysTrackTab('search');
                            preview.innerHTML = '<div style="color:#27ae60; font-size:12px;"><i class="fas fa-check-circle"></i> QR Code detected: ' + result.text + '</div>';
                            setTimeout(() => {
                                sysTrackDocument();
                                document.getElementById('sUploadContainer').style.display = 'none';
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

        document.getElementById('sTrackCodeInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { sysTrackDocument(); }
        });
    </script>
</x-app-layout>
