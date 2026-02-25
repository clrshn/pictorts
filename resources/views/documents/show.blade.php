<x-app-layout>
    <x-slot name="header">
        <h1>Document Track</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('documents.index') }}">Documents</a> / {{ $document->dts_number }}</div>
    </x-slot>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
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
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px 32px; font-size:13px;">
                <div><strong>Transaction Number:</strong> {{ $document->dts_number }}</div>
                <div><strong>Direction:</strong> {{ $document->direction }}</div>
                <div><strong>Document Type:</strong> {{ $document->document_type }}</div>
                <div><strong>PICTO No.:</strong> {{ $document->picto_number ?? '—' }}</div>
                <div><strong>Originating Office:</strong> {{ $document->originatingOffice->name ?? '—' }}</div>
                <div><strong>Date:</strong> {{ $document->date_received ? $document->date_received->format('F d, Y') : ($document->created_at ? $document->created_at->format('F d, Y h:i A') : '—') }}</div>
                <div style="grid-column:span 2;"><strong>Subject:</strong> {{ $document->subject }}</div>
                <div><strong>Action Required:</strong> {{ $document->action_required ?? '—' }}</div>
                <div><strong>Endorsed To:</strong> {{ $document->endorsed_to ?? '—' }}</div>
                <div><strong>Current Location:</strong> {{ $document->currentOffice->code ?? '—' }}</div>
                <div><strong>Current Holder:</strong> {{ $document->holder->name ?? '—' }}</div>
                <div><strong>Encoded By:</strong> {{ $document->encoder->name ?? '—' }}</div>
                <div><strong>Received Online:</strong> {{ $document->received_via_online ? 'Yes' : 'No' }}</div>
                <div style="grid-column:span 2;"><strong>Remarks:</strong> {{ $document->remarks ?? '—' }}</div>
                @if($document->shared_drive_link)
                    <div style="grid-column:span 2;"><strong>Shared Drive:</strong> <a href="{{ $document->shared_drive_link }}" target="_blank" style="color:#c0392b;">{{ $document->shared_drive_link }}</a></div>
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
            <button type="submit" class="btn-green" onclick="return confirm('Mark this document as COMPLETED?')"><i class="fas fa-check-square"></i> Mark as Completed</button>
        </form>
    </div>
    @endif

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
