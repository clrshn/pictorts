<x-app-layout>
    <x-slot name="header">
        <h1>Track Document</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / Track Document</div>
    </x-slot>

    <div class="filter-box" style="max-width:600px;">
        <h3><i class="fas fa-search"></i> Track Document</h3>

        <!-- Instruction -->
        <div style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:4px; padding:10px 14px; font-size:12px; color:#555; margin-bottom:16px;">
            Enter the tracking code below to <strong>track</strong> document
        </div>

        <!-- Search Panel -->
        <div id="sPanelSearch">
            <div style="position:relative; margin-bottom:16px;">
                <input type="text" id="sTrackCodeInput" placeholder="Enter tracking code (e.g., PICTO-LETTER-2026-0001)" class="form-control" style="padding-right:40px;">
                <i class="fas fa-search" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#c0392b; font-size:16px;"></i>
            </div>
            <button type="button" onclick="sysTrackDocument()" id="sTrackBtn" class="btn-red" style="width:100%; justify-content:center; padding:12px; border-radius:30px; font-size:14px; letter-spacing:1px;">
                TRACK &nbsp;<i class="fas fa-arrow-right"></i>
            </button>
            <div id="sTrackError" style="display:none; margin-top:12px; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; padding:10px 14px; border-radius:4px; font-size:13px;"></div>
            <div id="sTrackLoading" style="display:none; margin-top:12px; text-align:center; color:#999; font-size:13px;">
                <i class="fas fa-spinner fa-spin"></i> Searching...
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
                    <div><span style="color:#888;">Tracking Code:</span> <strong id="sResTrackingNo"></strong></div>
                    <div><span style="color:#888;">Direction:</span> <strong id="sResDirection"></strong></div>
                    <div><span style="color:#888;">Document Type:</span> <strong id="sResDocType"></strong></div>
                    <div><span style="color:#888;">Transaction Number:</span> <strong id="sResIctu"></strong></div>
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
                document.getElementById('sResTrackingNo').textContent = d.dts_number;
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
                document.getElementById('sResIctu').textContent = d.doc_number;
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
                    if (d.status === 'DONE' || d.status === 'COMPLETED') {
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

        document.getElementById('sTrackCodeInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { sysTrackDocument(); }
        });
    </script>
</x-app-layout>
