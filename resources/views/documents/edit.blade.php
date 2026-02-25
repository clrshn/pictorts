<x-app-layout>
    <x-slot name="header">
        <h1>Edit Document</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('documents.index') }}">Documents</a> / Edit</div>
    </x-slot>

    @if($errors->any())
        <div class="alert-error">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px;">
            <i class="fas fa-edit"></i> EDIT DOCUMENT — {{ $document->dts_number }}
        </div>

        <div style="padding:24px;">
            <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px;">
                    <div class="form-group">
                        <label>Document Type <span style="color:#c0392b">*</span></label>
                        <select name="document_type" class="form-control" required>
                            @foreach(['MEMO','EO','SO','LETTER','SP','OTHERS'] as $t)
                                <option value="{{ $t }}" {{ $document->document_type === $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Direction <span style="color:#c0392b">*</span></label>
                        <select name="direction" class="form-control" required>
                            <option value="INCOMING" {{ $document->direction === 'INCOMING' ? 'selected' : '' }}>INCOMING</option>
                            <option value="OUTGOING" {{ $document->direction === 'OUTGOING' ? 'selected' : '' }}>OUTGOING</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>PICTO No.</label>
                        <input type="text" name="picto_number" class="form-control" value="{{ old('picto_number', $document->picto_number) }}">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px;">
                    <div class="form-group">
                        <label>Originating Office <span style="color:#c0392b">*</span></label>
                        <select name="originating_office" class="form-control" required>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" {{ $document->originating_office == $office->id ? 'selected' : '' }}>{{ $office->code }} – {{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>To (Destination Office)</label>
                        <select name="to_office" class="form-control">
                            <option value="">Select Office</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" {{ $document->to_office == $office->id ? 'selected' : '' }}>{{ $office->code }} – {{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Document No.</label>
                        <input type="text" name="doc_number" class="form-control" value="{{ old('doc_number', $document->doc_number) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Subject / Title <span style="color:#c0392b">*</span></label>
                    <input type="text" name="subject" class="form-control" value="{{ old('subject', $document->subject) }}" required>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Action Required</label>
                        <textarea name="action_required" class="form-control" rows="2">{{ old('action_required', $document->action_required) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Actor/s | Endorsed To</label>
                        <input type="text" name="endorsed_to" class="form-control" value="{{ old('endorsed_to', $document->endorsed_to) }}">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px;">
                    <div class="form-group">
                        <label>Date Received</label>
                        <input type="date" name="date_received" class="form-control" value="{{ old('date_received', $document->date_received?->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label>Received via Online?</label>
                        <select name="received_via_online" class="form-control">
                            <option value="0" {{ !$document->received_via_online ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $document->received_via_online ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Shared Drive Link</label>
                        <input type="url" name="shared_drive_link" class="form-control" value="{{ old('shared_drive_link', $document->shared_drive_link) }}">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            @foreach(['ONGOING','DELIVERED','COMPLETED'] as $s)
                                <option value="{{ $s }}" {{ $document->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $document->remarks) }}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label>Add More Files</label>
                    <input type="file" name="files[]" multiple accept=".pdf,.jpg,.jpeg,.png,.gif" class="form-control" style="padding:6px;">
                </div>

                <div style="display:flex; gap:10px; margin-top:8px;">
                    <button type="submit" class="btn-red"><i class="fas fa-save"></i> Update Document</button>
                    <a href="{{ route('documents.show', $document) }}" class="btn-gray"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
