<x-app-layout>
    <x-slot name="header">
        <h1>Add New Document</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('documents.index') }}">Documents</a> / Add New</div>
    </x-slot>

    @if($errors->any())
        <div class="alert-error">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px;">
            <i class="fas fa-file-alt"></i> DOCUMENT ENCODING FORM
        </div>

        <div style="padding:24px;">
            <p style="font-size:12px; color:#999; margin-bottom:16px;">Tracking Code and PICTO Number will be auto-generated upon saving</p>
            <p style="font-size:11px; color:#999; margin-bottom:16px;">
                <strong>Tracking Code:</strong> {YEAR}{12-CHARACTER CODE} (e.g., 2026KTAYQWWBUEDG)<br>
                <strong>PICTO Number:</strong> PICTO-{OFFICE}-{TYPE}-{YEAR}-{6-digit SEQ} (e.g., PICTO-BAC-LETTER-2026-000001)
            </p>

            <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Row 1 -->
                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px;">
                    <div class="form-group">
                        <label>Document Type <span style="color:#c0392b">*</span></label>
                        <select name="document_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="MEMO" {{ old('document_type') === 'MEMO' ? 'selected' : '' }}>MEMO</option>
                            <option value="EO" {{ old('document_type') === 'EO' ? 'selected' : '' }}>EO – Executive Order</option>
                            <option value="SO" {{ old('document_type') === 'SO' ? 'selected' : '' }}>SO – Special Order</option>
                            <option value="LETTER" {{ old('document_type') === 'LETTER' ? 'selected' : '' }}>LETTER</option>
                            <option value="SP" {{ old('document_type') === 'SP' ? 'selected' : '' }}>SP – Special Permit</option>
                            <option value="OTHERS" {{ old('document_type') === 'OTHERS' ? 'selected' : '' }}>OTHERS</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Direction <span style="color:#c0392b">*</span></label>
                        <select name="direction" class="form-control" required>
                            <option value="">Select</option>
                            <option value="INCOMING" {{ old('direction') === 'INCOMING' ? 'selected' : '' }}>INCOMING</option>
                            <option value="OUTGOING" {{ old('direction') === 'OUTGOING' ? 'selected' : '' }}>OUTGOING</option>
                        </select>
                    </div>
                </div>

                <!-- Row 2 -->
                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px;">
                    <div class="form-group">
                        <label>Originating Office <span style="color:#c0392b">*</span></label>
                        <select name="originating_office" class="form-control" required>
                            <option value="">Select Office</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" {{ old('originating_office') == $office->id ? 'selected' : '' }}>{{ $office->code }} – {{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>To (Destination Office)</label>
                        <select name="to_office" class="form-control">
                            <option value="">Select Office</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" {{ old('to_office') == $office->id ? 'selected' : '' }}>{{ $office->code }} – {{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Subject -->
                <div class="form-group">
                    <label>Subject / Title <span style="color:#c0392b">*</span></label>
                    <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
                </div>

                <!-- Memorandum Number (Optional) -->
                <div class="form-group">
                    <label>Memorandum Number <small style="color:#999;">(Optional - Only for MEMO documents)</small></label>
                    <input type="text" name="memorandum_number" class="form-control" value="{{ old('memorandum_number') }}" placeholder="e.g., MEMO-2026-001">
                </div>

                <!-- Particulars (Optional) -->
                <div class="form-group">
                    <label>Particulars <small style="color:#999;">(Optional)</small></label>
                    <textarea name="particulars" class="form-control" rows="3" placeholder="Enter specific details or particulars about this document...">{{ old('particulars') }}</textarea>
                </div>

                <!-- Action & Endorsed -->
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Action Required</label>
                        <textarea name="action_required" class="form-control" rows="2">{{ old('action_required') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Actor/s | Endorsed To</label>
                        <input type="text" name="endorsed_to" class="form-control" value="{{ old('endorsed_to') }}">
                    </div>
                </div>

                <!-- Date, Online, Link -->
                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px;">
                    <div class="form-group">
                        <label>Date Received <span style="color:#c0392b">*</span></label>
                        <input type="date" name="date_received" class="form-control" value="{{ old('date_received') }}" required>
                        <small style="color:#999;">Used for tracking number generation (year)</small>
                    </div>
                    <div class="form-group">
                        <label>Received via Online?</label>
                        <select name="received_via_online" class="form-control">
                            <option value="0" {{ old('received_via_online') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('received_via_online') == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Shared Drive Link</label>
                        <input type="url" name="shared_drive_link" class="form-control" value="{{ old('shared_drive_link') }}" placeholder="https://...">
                    </div>
                </div>

                <!-- Remarks -->
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control" rows="2">{{ old('remarks') }}</textarea>
                </div>

                <!-- File Upload -->
                <div class="form-group">
                    <label>Attach Files (PDF, Images)</label>
                    <input type="file" name="files[]" multiple accept=".pdf,.jpg,.jpeg,.png,.gif" class="form-control" style="padding:6px;">
                    <small style="color:#999;">Max 10MB per file. You can select multiple files.</small>
                </div>

                <!-- Buttons -->
                <div style="display:flex; gap:10px; margin-top:8px;">
                    <button type="submit" class="btn-red"><i class="fas fa-save"></i> Save Document</button>
                    <a href="{{ route('documents.index') }}" class="btn-gray"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>