<x-app-layout>
    <x-slot name="header">
        <h1>Document Track</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('documents.index') }}">Documents</a> / {{ $document->dts_number }}</div>
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

    <!-- Document Details Card -->
    <div class="table-card" style="margin-bottom:20px;">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px; display:flex; justify-content:space-between; align-items:center;">
            <span><i class="fas fa-file-alt"></i> Document Track</span>
            <div style="display:flex; gap:8px;">
                <a href="{{ route('documents.edit', $document) }}" class="btn-orange" style="padding:4px 12px;"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ route('documents.index') }}" class="btn-gray" style="padding:4px 12px;"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <div style="padding:24px;">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px 32px; font-size:13px;">
                <!-- Subject (Full Width, Top) -->
                <div style="grid-column:span 2; border-left:3px solid #27ae60; padding-left:12px; margin-bottom:12px;">
                    <div><strong>Subject:</strong> {{ $document->subject }}</div>
                </div>
                
                <!-- Particulars (Full Width) -->
                <div style="grid-column:span 2; border-left:3px solid #e67e22; padding-left:12px; margin-bottom:12px;">
                    <div><strong>Particulars:</strong> {{ $document->particulars ?? '—' }}</div>
                </div>
                
                <!-- Document Identification -->
                <div style="border-left:3px solid #c0392b; padding-left:12px;">
                    <div style="margin-bottom:8px;"><strong>Tracking Code:</strong> <span style="font-family:monospace; color:#c0392b;">{{ $document->dts_number }}</span></div>
                    <div style="margin-bottom:8px;"><strong>PICTO No:</strong> <span style="font-family:monospace; color:#2c3e50;">{{ $document->doc_number }}</span></div>
                    <div><strong>Memorandum No:</strong> <span style="font-family:monospace; color:#2c3e50;">{{ $document->memorandum_number ?? '—' }}</span></div>
                </div>
                
                <!-- Document Details -->
                <div style="border-left:3px solid #3498db; padding-left:12px;">
                    <div style="margin-bottom:8px;"><strong>Direction:</strong> {{ $document->direction }}</div>
                    <div style="margin-bottom:8px;"><strong>Originating Office:</strong> {{ $document->originatingOffice->name ?? '—' }}</div>
                    <div><strong>Date:</strong> {{ $document->date_received ? $document->date_received->format('F d, Y') : ($document->created_at ? $document->created_at->format('F d, Y h:i A') : '—') }}</div>
                </div>
                
                <!-- Action Information -->
                <div style="border-left:3px solid #f39c12; padding-left:12px;">
                    <div style="margin-bottom:8px;"><strong>Action Required:</strong> {{ $document->action_required ?? '—' }}</div>
                    <div><strong>Endorsed To:</strong> {{ $document->endorsed_to ?? '—' }}</div>
                </div>
                
                <!-- Current Status -->
                <div style="border-left:3px solid #8e44ad; padding-left:12px;">
                    <div style="margin-bottom:8px;"><strong>Current Location:</strong> {{ $document->currentOffice->code ?? '—' }}</div>
                    <div style="margin-bottom:8px;"><strong>Current Holder:</strong> {{ $document->holder->name ?? '—' }}</div>
                    <div><strong>Status:</strong> <span class="badge badge-{{ strtolower($document->status) }}">{{ $document->status }}</span></div>
                </div>
                
                <!-- Additional Information -->
                <div style="border-left:3px solid #95a5a6; padding-left:12px;">
                    <div style="margin-bottom:8px;"><strong>Encoded By:</strong> {{ $document->encoder->name ?? '—' }}</div>
                    <div><strong>Received Online:</strong> {{ $document->received_via_online ? 'Yes' : 'No' }}</div>
                </div>
                
                <!-- Remarks (Full Width) -->
                <div style="grid-column:span 2; border-left:3px solid #7f8c8d; padding-left:12px;">
                    <div><strong>Remarks:</strong> {{ $document->remarks ?? '—' }}</div>
                </div>
                
                @if($document->shared_drive_link)
                    <div style="grid-column:span 2; border-left:3px solid #3498db; padding-left:12px;">
                        <div><strong>Shared Drive:</strong> <a href="{{ $document->shared_drive_link }}" target="_blank" style="color:#c0392b;">{{ $document->shared_drive_link }}</a></div>
                    </div>
                @endif
            </div>

            <div style="margin-top:12px;">
                @php
                    $badgeClass = match($document->status) {
                        'ONGOING' => 'badge-ongoing',
                        'DELIVERED' => 'badge-delivered',
                        'COMPLETED' => 'badge-completed',
                        default => ''
                    };
                @endphp
                <strong>Status:</strong> <span class="badge {{ $badgeClass }}">{{ $document->status }}</span>
            </div>
        </div>
    </div>

    <!-- Attached Files -->
    @if($document->files->count())
    <div class="table-card" style="margin-bottom:20px;">
        <div style="background:#333; color:#fff; padding:10px 20px; font-weight:600; font-size:13px;">
            <i class="fas fa-paperclip"></i> Attached Files
        </div>
        <div style="padding:16px;">
            @foreach($document->files as $file)
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

    <!-- Forward / Route Document -->
    @if($document->status !== 'COMPLETED')
    <div class="table-card" style="margin-bottom:20px;">
        <div style="background:#2c3e50; color:#fff; padding:10px 20px; font-weight:600; font-size:13px;">
            <i class="fas fa-share"></i> Forward / Route Document
        </div>
        <div style="padding:16px;">
            <form method="POST" action="{{ route('documents.route', $document) }}" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
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

            @php $pendingRoute = $document->routes->whereNull('datetime_received')->last(); @endphp
            @if($pendingRoute)
                <form method="POST" action="{{ route('documents.receive', $document) }}" style="margin-top:12px;">
                    @csrf
                    <button type="submit" class="btn-green"><i class="fas fa-check"></i> Confirm Received at {{ $pendingRoute->toOffice->code ?? '' }}</button>
                </form>
            @endif
        </div>
    </div>

    <!-- Mark as Completed -->
    <div style="margin-bottom:20px;">
        <form method="POST" action="{{ route('documents.update', $document) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="document_type" value="{{ $document->document_type }}">
            <input type="hidden" name="direction" value="{{ $document->direction }}">
            <input type="hidden" name="originating_office" value="{{ $document->originating_office }}">
            <input type="hidden" name="subject" value="{{ $document->subject }}">
            <input type="hidden" name="status" value="COMPLETED">
            <button type="submit" class="btn-green" onclick="event.preventDefault(); confirmMarkCompleted();"><i class="fas fa-check-square"></i> Mark as Completed</button>
        </form>
    </div>
    @endif

    <!-- Routing History -->
    <div class="table-card">
        <div style="background:#333; color:#fff; padding:10px 20px; font-weight:600; font-size:13px;">
            <i class="fas fa-route"></i> Routing History
        </div>
        <div style="padding:20px;">
            @if($document->routes->count())
                <div style="position:relative; padding-left:30px;">
                    <div style="position:absolute; left:12px; top:0; bottom:0; width:3px; background:#e0e0e0;"></div>
                    @foreach($document->routes->sortBy('datetime_released') as $route)
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
                                            <i class="fas fa-plus-circle" style="margin-right:6px; color:#27ae60;"></i> Document Created at {{ $route->fromOffice->code ?? '?' }}
                                            <span class="badge badge-completed" style="margin-left:8px;">CREATED</span>
                                        @elseif(str_contains($route->remarks, 'COMPLETED'))
                                            <i class="fas fa-check-circle" style="margin-right:6px; color:#27ae60;"></i> Document Completed at {{ $route->fromOffice->code ?? '?' }}
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
                                        Forwarded by: {{ $route->releasedByUser->name ?? '—' }} — {{ $route->datetime_released?->format('M d, Y h:i A') }}
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
                <p style="text-align:center; color:#666; padding:20px;">No routing history available</p>
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

// Modern confirmation for Mark as Completed
function confirmMarkCompleted() {
    // Create a simple modern confirmation dialog
    const confirmDialog = document.createElement('div');
    confirmDialog.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-left: 4px solid #f39c12;
        min-width: 300px;
        max-width: 400px;
        z-index: 9999;
    `;
    
    confirmDialog.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
            <div style="font-weight: 600; font-size: 14px; color: #2c3e50; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-exclamation-triangle" style="color: #f39c12;"></i>
                Mark as Completed
            </div>
            <button onclick="this.closest('.confirm-dialog').remove()" style="background: none; border: none; color: #7f8c8d; font-size: 18px; cursor: pointer; padding: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">&times;</button>
        </div>
        <div style="color: #555; font-size: 13px; line-height: 1.4; margin-bottom: 12px;">
            Are you sure you want to mark this document as COMPLETED?<br><br><strong>This action will update the document status and cannot be undone!</strong>
        </div>
        <div style="margin-top: 12px; display: flex; gap: 8px; justify-content: flex-end;">
            <button onclick="this.closest('.confirm-dialog').remove()" style="padding: 6px 12px; border: none; border-radius: 4px; font-size: 12px; font-weight: 500; cursor: pointer; background: #ecf0f1; color: #555;">Cancel</button>
            <button onclick="confirmComplete()" style="padding: 6px 12px; border: none; border-radius: 4px; font-size: 12px; font-weight: 500; cursor: pointer; background: #e74c3c; color: white;">Mark as Completed</button>
        </div>
    `;
    
    confirmDialog.className = 'confirm-dialog';
    document.body.appendChild(confirmDialog);
    
    // Add backdrop
    const backdrop = document.createElement('div');
    backdrop.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.4);
        z-index: 9998;
    `;
    backdrop.onclick = function() {
        confirmDialog.remove();
        backdrop.remove();
    };
    document.body.appendChild(backdrop);
    
    // Global function for confirm action
    window.confirmComplete = function() {
        confirmDialog.remove();
        backdrop.remove();
        document.querySelector('form[action*="update"]').submit();
    };
}
</script>
