<x-guest-layout>
    <!-- Logo -->
    <div class="login-logo">
        <div style="font-size:40px; margin-bottom:6px;">
            <i class="fas fa-file-shield" style="color:#1a1a6c;"></i>
        </div>
        <div class="logo-title">
            <span class="red">PICTO </span><span class="blue">RECORDS</span><br>
            <span class="blue">& TRACKING</span><br>
            <span class="red">SYSTEM</span>
        </div>
        <div class="logo-sub">PROVINCIAL GOVERNMENT OF LA UNION</div>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="login-error" style="background:#d4edda; color:#155724; border-color:#c3e6cb;">
            {{ session('status') }}
        </div>
    @endif

    <!-- Errors -->
    @if ($errors->any())
        <div class="login-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="login-form-group">
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Username">
        </div>

        <!-- Password -->
        <div class="login-form-group">
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Password">
        </div>

        <!-- Remember Me + Forgot Password -->
        <div class="login-options">
            <label for="remember_me">
                <input id="remember_me" type="checkbox" name="remember">
                Remember me
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot Password</a>
            @endif
        </div>

        <!-- Login Button -->
        <button type="submit" class="btn-login">
            LOG IN <i class="fas fa-arrow-right"></i>
        </button>
    </form>

    <!-- Divider -->
    <div class="login-divider">or</div>

    <!-- Track Document Button -->
    <button type="button" class="btn-track" onclick="document.getElementById('trackModal').style.display='flex'">
        TRACK DOCUMENT <i class="fas fa-search-location"></i>
    </button>

    <!-- Footer -->
    <div class="login-footer">
        &copy; Copyright {{ date('Y') }}. All right reserved. <a href="#">PICTO Records & Tracking System</a> V1.0
    </div>

    <!-- Track Document Modal -->
    <div id="trackModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:8px; max-width:540px; width:95%; max-height:90vh; overflow-y:auto; box-shadow:0 10px 40px rgba(0,0,0,0.3); position:relative;">
            <!-- Red Header -->
            <div style="background:#c0392b; padding:20px 24px; border-radius:8px 8px 0 0; text-align:center; position:relative;">
                <button onclick="document.getElementById('trackModal').style.display='none'" style="position:absolute; top:10px; right:14px; background:#333; color:#fff; border:none; width:26px; height:26px; border-radius:50%; font-size:14px; cursor:pointer; font-weight:700;">&times;</button>
                <div style="font-size:24px; margin-bottom:4px;"><i class="fas fa-file-shield" style="color:#fff;"></i></div>
                <div style="color:#fff; font-weight:800; font-size:16px; line-height:1.2;">
                    <span style="color:#1a1a6c;">PICTO</span> RECORDS<br>& TRACKING<br><span style="color:#ffe082;">SYSTEM</span>
                </div>
            </div>

            <!-- Body -->
            <div style="padding:20px 24px;">
                <!-- Instruction -->
                <div style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:4px; padding:10px 14px; font-size:12px; color:#555; margin-bottom:16px;">
                    Use the QR Code scanner and scan image file under Scan Tab or just enter the QR Code under Search Tab to <strong>track</strong> document
                </div>

                <!-- Search / Scan Tabs -->
                <div style="display:flex; gap:0; margin-bottom:16px;">
                    <button id="tabSearch" onclick="showTrackTab('search')" style="padding:10px 28px; font-size:13px; font-weight:700; border:none; cursor:pointer; border-radius:4px 0 0 4px; background:#c0392b; color:#fff;">Search</button>
                    <button id="tabScan" onclick="showTrackTab('scan')" style="padding:10px 28px; font-size:13px; font-weight:700; border:none; cursor:pointer; border-radius:0 4px 4px 0; background:#e9ecef; color:#555;">Scan</button>
                </div>

                <!-- Search Panel -->
                <div id="panelSearch">
                    <div style="position:relative; margin-bottom:16px;">
                        <input type="text" id="trackCodeInput" placeholder="QR Code / Tracking Number / DTS Number" style="width:100%; padding:10px 40px 10px 14px; border:1px solid #ddd; border-radius:4px; font-size:13px; outline:none;">
                        <i class="fas fa-qrcode" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#c0392b; font-size:16px;"></i>
                    </div>
                    <button type="button" onclick="trackDocument()" id="trackBtn" style="width:100%; padding:12px; background:#fff; color:#555; border:2px solid #ddd; border-radius:30px; font-size:14px; font-weight:700; cursor:pointer; letter-spacing:1px; transition: all 0.2s;">
                        TRACK &nbsp;<i class="fas fa-arrow-right"></i>
                    </button>
                    <div id="trackError" style="display:none; margin-top:12px; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; padding:10px 14px; border-radius:4px; font-size:13px;"></div>
                    <div id="trackLoading" style="display:none; margin-top:12px; text-align:center; color:#999; font-size:13px;">
                        <i class="fas fa-spinner fa-spin"></i> Searching...
                    </div>
                </div>

                <!-- Scan Panel -->
                <div id="panelScan" style="display:none;">
                    <div style="border:1px solid #ddd; border-radius:4px; padding:20px; text-align:center; min-height:150px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                            <span style="font-size:14px; font-weight:600; color:#c0392b;">Code Scanner</span>
                            <span id="scanStatus" style="font-size:12px; color:#999;">IDLE</span>
                        </div>
                        
                        <!-- Camera View -->
                        <div id="cameraContainer" style="display:none; position:relative; margin-bottom:12px;">
                            <video id="video" style="width:100%; max-width:320px; height:240px; border-radius:4px; background:#000;"></video>
                            <canvas id="canvas" style="display:none;"></canvas>
                            <button type="button" onclick="stopCamera()" style="position:absolute; top:8px; right:8px; background:rgba(0,0,0,0.7); color:#fff; border:none; width:24px; height:24px; border-radius:50%; font-size:12px; cursor:pointer;">&times;</button>
                        </div>

                        <!-- File Upload -->
                        <div id="uploadContainer" style="display:none; margin-bottom:12px;">
                            <input type="file" id="fileInput" accept="image/*" style="display:none;" onchange="handleFileUpload(event)">
                            <button type="button" onclick="document.getElementById('fileInput').click()" style="padding:8px 16px; background:#2980b9; color:#fff; border:none; border-radius:4px; font-size:13px; font-weight:600; cursor:pointer;">
                                <i class="fas fa-upload"></i> Choose Image
                            </button>
                            <div id="uploadPreview" style="margin-top:8px;"></div>
                        </div>

                        <div style="padding:20px 0;">
                            <button type="button" id="startCameraBtn" onclick="startCamera()" style="padding:10px 24px; background:#c0392b; color:#fff; border:none; border-radius:4px; font-size:13px; font-weight:600; cursor:pointer;">
                                <i class="fas fa-camera"></i> Start Camera
                            </button>
                            <div style="margin-top:10px;">
                                <button type="button" onclick="showFileUpload()" style="background:none; border:none; color:#2980b9; font-size:12px; text-decoration:underline; cursor:pointer;">
                                    <i class="fas fa-image"></i> Scan an Image File
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Track Result Modal -->
    <div id="trackResultModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10000; align-items:flex-start; justify-content:center; padding-top:30px;">
        <div style="background:#fff; border-radius:8px; max-width:700px; width:95%; max-height:85vh; overflow-y:auto; box-shadow:0 10px 40px rgba(0,0,0,0.3); position:relative;">
            <!-- Header -->
            <div style="padding:16px 24px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="font-size:18px; font-weight:700; color:#2d3436; margin:0;">Document Track</h3>
                <button onclick="document.getElementById('trackResultModal').style.display='none'" style="background:#c0392b; color:#fff; border:none; width:28px; height:28px; border-radius:4px; font-size:14px; cursor:pointer; font-weight:700;">&times;</button>
            </div>

            <!-- Body -->
            <div style="padding:20px 24px;">
                <h4 style="text-align:center; color:#c0392b; font-size:16px; font-weight:700; margin-bottom:16px;">This document is confidential!</h4>

                <!-- Document Details Grid -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px 24px; font-size:13px; margin-bottom:24px;">
                    <div><span style="color:#888;">Tracking Code:</span> <strong id="resTrackingNo"></strong></div>
                    <div><span style="color:#888;">Direction:</span> <strong id="resDirection"></strong></div>
                    <div><span style="color:#888;">Document Type:</span> <strong id="resDocType"></strong></div>
                    <div><span style="color:#888;">Transaction Number:</span> <strong id="resIctu"></strong></div>
                    <div><span style="color:#888;">Originating Office:</span> <strong id="resOrigin"></strong></div>
                    <div><span style="color:#888;">Action Required:</span> <strong id="resAction"></strong></div>
                    <div><span style="color:#888;">Subject:</span> <strong id="resSubject"></strong></div>
                    <div><span style="color:#888;">Endorsed To:</span> <strong id="resEndorsed"></strong></div>
                    <div><span style="color:#888;">Remarks:</span> <strong id="resRemarks"></strong></div>
                    <div><span style="color:#888;">Date:</span> <strong id="resDate"></strong></div>
                    <div><span style="color:#888;">Date Received:</span> <strong id="resDateReceived"></strong></div>
                    <div><span style="color:#888;">Current Location:</span> <strong id="resLocation"></strong></div>
                    <div><span style="color:#888;">Current Holder:</span> <strong id="resHolder"></strong></div>
                    <div><span style="color:#888;">Status:</span> <strong id="resStatus"></strong></div>
                </div>

                <!-- Tracks Section -->
                <h4 style="font-size:16px; font-weight:700; color:#2d3436; margin-bottom:12px;">Tracks</h4>

                <!-- Timeline / Table Tabs -->
                <div style="display:flex; gap:0; margin-bottom:12px; border-bottom:2px solid #e5e7eb;">
                    <button id="tabTimeline" onclick="showTracksTab('timeline')" style="padding:8px 20px; font-size:13px; font-weight:600; border:none; cursor:pointer; background:transparent; color:#c0392b; border-bottom:2px solid #c0392b; margin-bottom:-2px;">Timeline Tracking</button>
                    <button id="tabTable" onclick="showTracksTab('table')" style="padding:8px 20px; font-size:13px; font-weight:600; border:none; cursor:pointer; background:transparent; color:#888; border-bottom:2px solid transparent; margin-bottom:-2px;">Table Tracking</button>
                </div>

                <!-- Timeline View -->
                <div id="panelTimeline">
                    <div id="timelineContent" style="background:#f4f6f9; border-radius:6px; padding:20px; min-height:80px; position:relative;"></div>
                </div>

                <!-- Table View -->
                <div id="panelTable" style="display:none;">
                    <table style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr style="background:#3498db;">
                                <th style="padding:10px 14px; color:#fff; font-size:12px; font-weight:600; text-align:left;">DATE</th>
                                <th style="padding:10px 14px; color:#fff; font-size:12px; font-weight:600; text-align:left;">TIME</th>
                                <th style="padding:10px 14px; color:#fff; font-size:12px; font-weight:600; text-align:left;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="tableTrackBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div style="padding:12px 24px; text-align:right; border-top:1px solid #e5e7eb;">
                <button onclick="document.getElementById('trackResultModal').style.display='none'" style="padding:8px 24px; background:#c0392b; color:#fff; border:none; border-radius:4px; font-size:13px; font-weight:600; cursor:pointer;">Close</button>
            </div>
        </div>
    </div>

    <script>
        function showTrackTab(tab) {
            document.getElementById('panelSearch').style.display = tab === 'search' ? 'block' : 'none';
            document.getElementById('panelScan').style.display = tab === 'scan' ? 'block' : 'none';
            document.getElementById('tabSearch').style.background = tab === 'search' ? '#c0392b' : '#e9ecef';
            document.getElementById('tabSearch').style.color = tab === 'search' ? '#fff' : '#555';
            document.getElementById('tabScan').style.background = tab === 'scan' ? '#c0392b' : '#e9ecef';
            document.getElementById('tabScan').style.color = tab === 'scan' ? '#fff' : '#555';
        }

        function showTracksTab(tab) {
            document.getElementById('panelTimeline').style.display = tab === 'timeline' ? 'block' : 'none';
            document.getElementById('panelTable').style.display = tab === 'table' ? 'block' : 'none';
            document.getElementById('tabTimeline').style.color = tab === 'timeline' ? '#c0392b' : '#888';
            document.getElementById('tabTimeline').style.borderBottom = tab === 'timeline' ? '2px solid #c0392b' : '2px solid transparent';
            document.getElementById('tabTable').style.color = tab === 'table' ? '#c0392b' : '#888';
            document.getElementById('tabTable').style.borderBottom = tab === 'table' ? '2px solid #c0392b' : '2px solid transparent';
        }

        function trackDocument() {
            var code = document.getElementById('trackCodeInput').value.trim();
            if (!code) { alert('Please enter a tracking code.'); return; }

            document.getElementById('trackError').style.display = 'none';
            document.getElementById('trackLoading').style.display = 'block';
            document.getElementById('trackBtn').disabled = true;

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
                document.getElementById('trackLoading').style.display = 'none';
                document.getElementById('trackBtn').disabled = false;

                if (!data.found) {
                    document.getElementById('trackError').textContent = data.message;
                    document.getElementById('trackError').style.display = 'block';
                    return;
                }

                // Populate result modal
                var d = data.document;
                document.getElementById('resTrackingNo').textContent = d.dts_number;
                document.getElementById('resDocType').textContent = d.document_type;
                document.getElementById('resDirection').textContent = d.direction;
                document.getElementById('resOrigin').textContent = d.originating_office;
                document.getElementById('resSubject').textContent = d.subject;
                document.getElementById('resRemarks').textContent = d.remarks;
                document.getElementById('resDate').textContent = d.date;
                document.getElementById('resDateReceived').textContent = d.date_received;
                document.getElementById('resLocation').textContent = d.current_location;
                document.getElementById('resHolder').textContent = d.current_holder;
                document.getElementById('resStatus').textContent = d.status;
                document.getElementById('resIctu').textContent = d.doc_number;
                document.getElementById('resAction').textContent = d.action_required;
                document.getElementById('resEndorsed').textContent = d.endorsed_to;

                // Build timeline
                var routes = data.routes;
                var timelineHtml = '';
                if (routes.length === 0) {
                    timelineHtml = '<div style="text-align:center; color:#999; font-size:13px; padding:20px;">No routing history yet.</div>';
                } else {
                    timelineHtml += '<div style="position:relative; padding-left:30px;">';
                    // Start node
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

                    // End node
                    if (d.status === 'COMPLETED') {
                        timelineHtml += '<div style="position:relative; border-left:2px solid #ddd; margin-left:-14px; padding-left:22px;">';
                        timelineHtml += '<div style="position:absolute; left:-9px; top:2px; width:16px; height:16px; border-radius:50%; background:#c0392b; border:2px solid #fff;"></div>';
                        timelineHtml += '<div style="font-size:12px; font-weight:700; color:#c0392b;">End</div>';
                        timelineHtml += '</div>';
                    }
                    timelineHtml += '</div>';
                }
                document.getElementById('timelineContent').innerHTML = timelineHtml;

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
                document.getElementById('tableTrackBody').innerHTML = tableHtml;

                // Show result, hide track modal
                document.getElementById('trackModal').style.display = 'none';
                document.getElementById('trackResultModal').style.display = 'flex';
                showTracksTab('timeline');
            })
            .catch(function(err) {
                document.getElementById('trackLoading').style.display = 'none';
                document.getElementById('trackBtn').disabled = false;
                document.getElementById('trackError').textContent = 'An error occurred. Please try again.';
                document.getElementById('trackError').style.display = 'block';
            });
        }

        // QR Scanner Variables
        let codeReader = null;
        let currentStream = null;

        // Camera Scanner Functions
        function startCamera() {
            if (!codeReader) {
                codeReader = new ZXing.BrowserQRCodeReader();
            }

            const videoElement = document.getElementById('video');
            const startBtn = document.getElementById('startCameraBtn');
            const cameraContainer = document.getElementById('cameraContainer');
            const status = document.getElementById('scanStatus');

            startBtn.disabled = true;
            startBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Starting...';
            status.textContent = 'STARTING';
            status.style.color = '#f39c12';

            codeReader.decodeFromVideoDevice(null, videoElement, (result, err) => {
                if (result) {
                    document.getElementById('trackCodeInput').value = result.text;
                    showTrackTab('search');
                    stopCamera();
                    trackDocument();
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

        function stopCamera() {
            if (codeReader && currentStream) {
                codeReader.reset();
                currentStream = null;
            }
            document.getElementById('cameraContainer').style.display = 'none';
            document.getElementById('startCameraBtn').style.display = 'inline-block';
            document.getElementById('startCameraBtn').disabled = false;
            document.getElementById('startCameraBtn').innerHTML = '<i class="fas fa-camera"></i> Start Camera';
            document.getElementById('scanStatus').textContent = 'IDLE';
            document.getElementById('scanStatus').style.color = '#999';
        }

        // File Upload Functions
        function showFileUpload() {
            document.getElementById('uploadContainer').style.display = 'block';
        }

        function handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const preview = document.getElementById('uploadPreview');
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
                    
                    if (!codeReader) {
                        codeReader = new ZXing.BrowserQRCodeReader();
                    }

                    try {
                        const result = codeReader.decodeFromImageData(imageData);
                        if (result) {
                            document.getElementById('trackCodeInput').value = result.text;
                            showTrackTab('search');
                            preview.innerHTML = '<div style="color:#27ae60; font-size:12px;"><i class="fas fa-check-circle"></i> QR Code detected: ' + result.text + '</div>';
                            setTimeout(() => {
                                trackDocument();
                                document.getElementById('uploadContainer').style.display = 'none';
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

        // Allow Enter key to trigger track
        document.getElementById('trackCodeInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { trackDocument(); }
        });

        // Close modals when clicking outside
        document.getElementById('trackModal').addEventListener('click', function(e) { if (e.target === this) this.style.display = 'none'; });
        document.getElementById('trackResultModal').addEventListener('click', function(e) { if (e.target === this) this.style.display = 'none'; });

        // Cleanup camera when modal closes
        document.getElementById('trackModal').addEventListener('click', function(e) {
            if (e.target === this) {
                stopCamera();
                this.style.display = 'none';
            }
        });
    </script>
</x-guest-layout>
