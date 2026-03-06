<x-app-layout>
    <x-slot name="header">
        <h1>Financial Record</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('financial.index') }}">Financial</a> / Record #{{ $financial->id }}</div>
    </x-slot>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <!-- Record Details -->
    <div class="table-card" style="margin-bottom:20px;">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px; display:flex; justify-content:space-between; align-items:center;">
            <span><i class="fas fa-coins"></i> Financial Record Details</span>
            <div style="display:flex; gap:8px;">
                <a href="{{ route('financial.edit', $financial) }}" class="btn-orange" style="padding:4px 12px;"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ route('financial.index') }}" class="btn-gray" style="padding:4px 12px;"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div style="padding:24px;">
            @php
                $badgeClass = match($financial->status) {
                    'ACTIVE' => 'badge-active',
                    'CANCELLED' => 'badge-cancelled',
                    'FINISHED' => 'badge-finished',
                    default => ''
                };
            @endphp
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px 32px; font-size:13px;">
                <!-- Subject/Title (Full Width, Top) -->
                <div style="grid-column:span 2; border-left:3px solid #27ae60; padding-left:12px; margin-bottom:12px;">
                    <div><strong>Subject/Title:</strong> {{ $financial->description }}</div>
                </div>
                
                <!-- Financial Identification -->
                <div style="border-left:3px solid #c0392b; padding-left:12px;">
                    <div style="margin-bottom:8px;"><strong>Type:</strong> {{ $financial->type ?? '—' }}</div>
                    <div style="margin-bottom:8px;"><strong>Supplier:</strong> {{ $financial->supplier ?? '—' }}</div>
                    <div><strong>Office:</strong> {{ $financial->originOffice->code ?? '—' }} – {{ $financial->originOffice->name ?? '' }}</div>
                </div>
                
                <!-- Current Status -->
                <div style="border-left:3px solid #8e44ad; padding-left:12px;">
                    <div style="margin-bottom:8px;"><strong>Current Office:</strong> {{ $financial->currentOffice->code ?? '—' }}</div>
                    <div style="margin-bottom:8px;"><strong>Current Holder:</strong> {{ $financial->holder->name ?? '—' }}</div>
                    <div><strong>Status:</strong> <span class="badge {{ $badgeClass }}">{{ $financial->status }}</span></div>
                </div>
                
                <!-- Purchase Request -->
                <div style="border-left:3px solid #3498db; padding-left:12px;">
                    <div style="margin-bottom:8px;"><strong>PR Number:</strong> <span style="font-family:monospace; color:#2c3e50;">{{ $financial->pr_number ?? '—' }}</span></div>
                    <div><strong>PR Amount:</strong> <span style="color:#27ae60; font-weight:600;">{{ $financial->pr_amount ? '₱ ' . number_format($financial->pr_amount, 2) : '—' }}</span></div>
                </div>
                
                <!-- Purchase Order -->
                <div style="border-left:3px solid #f39c12; padding-left:12px;">
                    <div style="margin-bottom:8px;"><strong>PO Number:</strong> <span style="font-family:monospace; color:#2c3e50;">{{ $financial->po_number ?? '—' }}</span></div>
                    <div><strong>PO Amount:</strong> <span style="color:#27ae60; font-weight:600;">{{ $financial->po_amount ? '₱ ' . number_format($financial->po_amount, 2) : '—' }}</span></div>
                </div>
                
                <!-- Payment Information -->
                <div style="border-left:3px solid #e74c3c; padding-left:12px;">
                    <div style="margin-bottom:8px;"><strong>OBR Number:</strong> <span style="font-family:monospace; color:#2c3e50;">{{ $financial->obr_number ?? '—' }}</span></div>
                    <div><strong>Voucher Number:</strong> <span style="font-family:monospace; color:#2c3e50;">{{ $financial->voucher_number ?? '—' }}</span></div>
                </div>
                
                <!-- Remarks (Full Width) -->
                <div style="grid-column:span 2; border-left:3px solid #7f8c8d; padding-left:12px;">
                    <div><strong>Remarks:</strong> {{ $financial->remarks ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attachments -->
    @if($financial->attachments->count())
    <div class="table-card" style="margin-bottom:20px;">
        <div style="background:#333; color:#fff; padding:10px 20px; font-weight:600; font-size:13px;">
            <i class="fas fa-paperclip"></i> Attachments
        </div>
        <div style="padding:16px;">
            @foreach($financial->attachments as $file)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 12px; background:#f9f9f9; border-radius:4px; margin-bottom:6px;">
                    <span style="font-size:13px; color:#444;"><i class="fas fa-file"></i> {{ $file->file_name }}</span>
                    <div style="display:flex; gap:8px;">
                        @if(strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)) === 'pdf')
                            <button onclick="viewPdf('{{ asset('storage/' . $file->file_path) }}', '{{ $file->file_name }}')" class="btn-blue" style="padding:3px 10px;" title="View PDF"><i class="fas fa-eye"></i></button>
                        @endif
                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn-blue" style="padding:3px 10px;" title="Download"><i class="fas fa-download"></i></a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- PDF Viewer Modal -->
    <div id="pdfViewerModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; justify-content:center; align-items:center;">
        <div style="background:white; width:90%; height:90%; border-radius:8px; display:flex; flex-direction:column;">
            <div style="padding:16px; border-bottom:1px solid #e0e0e0; display:flex; justify-content:space-between; align-items:center;">
                <h3 id="pdfTitle" style="margin:0; color:#333;">PDF Viewer</h3>
                <button onclick="closePdfViewer()" style="background:none; border:none; font-size:24px; cursor:pointer; color:#666;">&times;</button>
            </div>
            <div style="flex:1; padding:16px; overflow:auto;">
                <iframe id="pdfFrame" style="width:100%; height:100%; border:none;" src=""></iframe>
            </div>
        </div>
    </div>

    <!-- Forward -->
    @if($financial->status === 'ACTIVE')
    <div class="table-card" style="margin-bottom:20px;">
        <div style="background:#2c3e50; color:#fff; padding:10px 20px; font-weight:600; font-size:13px;">
            <i class="fas fa-share"></i> Forward / Route Record
        </div>
        <div style="padding:16px;">
            <form method="POST" action="{{ route('financial.route', $financial) }}" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
                @csrf
                <div class="form-group" style="flex:1; min-width:200px; margin:0;">
                    <label>Forward To Office</label>
                    <select name="to_office" class="form-control" required>
                        <option value="">Select Office</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->code }} – {{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="flex:1; min-width:200px; margin:0;">
                    <label>Remarks</label>
                    <input type="text" name="remarks" class="form-control">
                </div>
                <button type="submit" class="btn-red" style="height:36px;"><i class="fas fa-paper-plane"></i> Forward</button>
            </form>

            @php $pendingRoute = $financial->routes->whereNull('datetime_received')->last(); @endphp
            @if($pendingRoute)
                <form method="POST" action="{{ route('financial.receive', $financial) }}" style="margin-top:12px;">
                    @csrf
                    <button type="submit" class="btn-green"><i class="fas fa-check"></i> Confirm Received at {{ $pendingRoute->toOffice->code ?? '' }}</button>
                </form>
            @endif
        </div>
    </div>
    @endif

    <!-- Routing History -->
    <div class="table-card">
        <div style="background:#333; color:#fff; padding:10px 20px; font-weight:600; font-size:13px;">
            <i class="fas fa-route"></i> Routing History
        </div>
        <div style="padding:20px;">
            @if($financial->routes->count())
                <div style="position:relative; padding-left:30px;">
                    <div style="position:absolute; left:12px; top:0; bottom:0; width:3px; background:#e0e0e0;"></div>
                    @foreach($financial->routes->sortBy('datetime_released') as $route)
                        <div style="position:relative; margin-bottom:24px;">
                            <div style="position:absolute; left:-24px; top:2px; width:20px; height:20px; border-radius:50%; background:{{ $route->datetime_received ? '#27ae60' : '#e67e22' }}; display:flex; align-items:center; justify-content:center;">
                                <i class="fas {{ 
                                    $route->from_office == $route->to_office 
                                        ? (str_contains($route->remarks, 'created') ? 'fa-plus' : (str_contains($route->remarks, 'COMPLETED') ? 'fa-check' : 'fa-info')) 
                                        : ($route->datetime_received ? 'fa-check' : 'fa-clock') 
                                }}" style="color:#fff; font-size:10px;"></i>
                            </div>
                            <div style="background:#f8f9fa; border-radius:6px; padding:12px 16px; border-left:3px solid {{ $route->datetime_received ? '#27ae60' : '#e67e22' }};">
                                <div style="font-size:13px; font-weight:600; color:#333;">
                                    @if($route->from_office == $route->to_office)
                                        @if(str_contains($route->remarks, 'created'))
                                            <i class="fas fa-plus-circle" style="margin-right:6px; color:#27ae60;"></i> Financial Record Created at {{ $route->fromOffice->code ?? '?' }}
                                            <span class="badge badge-completed" style="margin-left:8px;">CREATED</span>
                                        @elseif(str_contains($route->remarks, 'COMPLETED'))
                                            <i class="fas fa-check-circle" style="margin-right:6px; color:#27ae60;"></i> Financial Record Completed at {{ $route->fromOffice->code ?? '?' }}
                                            <span class="badge badge-completed" style="margin-left:8px;">COMPLETED</span>
                                        @else
                                            <i class="fas fa-info-circle" style="margin-right:6px; color:#3498db;"></i> {{ $route->fromOffice->code ?? '?' }}
                                            <span class="badge badge-completed" style="margin-left:8px;">UPDATED</span>
                                        @endif
                                    @else
                                        {{ $route->fromOffice->code ?? '?' }} <i class="fas fa-arrow-right" style="margin:0 6px; color:#999; font-size:11px;"></i> {{ $route->toOffice->code ?? '?' }}
                                        <span class="badge {{ $route->datetime_received ? 'badge-completed' : 'badge-ongoing' }}" style="margin-left:8px;">{{ $route->datetime_received ? 'RECEIVED' : 'IN TRANSIT' }}</span>
                                    @endif
                                </div>
                                <div style="font-size:12px; color:#888; margin-top:4px;">
                                    @if($route->from_office == $route->to_office)
                                        @if(str_contains($route->remarks, 'created'))
                                            Created by: {{ $route->releasedByUser->name ?? '—' }} — {{ $route->datetime_released?->format('M d, Y h:i A') }}
                                        @elseif(str_contains($route->remarks, 'COMPLETED'))
                                            Completed by: {{ $route->releasedByUser->name ?? '—' }} — {{ $route->datetime_released?->format('M d, Y h:i A') }}
                                        @else
                                            Updated by: {{ $route->releasedByUser->name ?? '—' }} — {{ $route->datetime_released?->format('M d, Y h:i A') }}
                                        @endif
                                    @else
                                        Released by: {{ $route->releasedByUser->name ?? '—' }} — {{ $route->datetime_released?->format('M d, Y h:i A') }}
                                    @endif
                                </div>
                                @if($route->datetime_received && $route->from_office != $route->to_office)
                                    <div style="font-size:12px; color:#888;">Received by: {{ $route->receivedByUser->name ?? '—' }} — {{ $route->datetime_received->format('M d, Y h:i A') }}</div>
                                @endif
                                @if($route->remarks)
                                    <div style="font-size:12px; color:#666; margin-top:4px; font-style:italic;">{{ $route->remarks }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="text-align:center; color:#999; padding:16px 0;">No routing history yet.</p>
            @endif
        </div>
    </div>
</x-app-layout>

<script>
function viewPdf(url, title) {
    document.getElementById('pdfTitle').textContent = title;
    document.getElementById('pdfFrame').src = url;
    document.getElementById('pdfViewerModal').style.display = 'flex';
}

function closePdfViewer() {
    document.getElementById('pdfViewerModal').style.display = 'none';
    document.getElementById('pdfFrame').src = '';
}

// Close modal when clicking outside
document.getElementById('pdfViewerModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePdfViewer();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePdfViewer();
    }
});
</script>
