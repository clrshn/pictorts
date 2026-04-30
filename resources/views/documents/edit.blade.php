<x-app-layout>
    <x-slot name="header">
        <h1>{{ !empty($isTravelOrder) ? 'Edit Travel Order' : 'Edit Document' }}</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('documents.index', !empty($isTravelOrder) ? ['type' => 'TO'] : []) }}">{{ !empty($isTravelOrder) ? 'Travel Orders' : 'Documents' }}</a> / Edit</div>
    </x-slot>

    @if($errors->any())
        <div class="alert-error">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @if(session('duplicate_warning'))
        <div class="alert alert-warning" style="margin-bottom:16px;">
            <strong>{{ session('duplicate_warning') }}</strong>
            @if(session('duplicate_candidates'))
                <ul style="margin:10px 0 0 18px; padding:0;">
                    @foreach(session('duplicate_candidates') as $candidate)
                        <li>{{ $candidate->dts_number }} - {{ $candidate->subject ?? $candidate->particulars ?? 'Untitled' }} ({{ $candidate->originatingOffice->code ?? 'N/A' }})</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px;">
            <i class="fas fa-edit"></i> EDIT DOCUMENT - {{ $document->dts_number }}
        </div>

        <div style="padding:24px;">
            <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:16px;">
                    <div class="form-group">
                        <label>Document Type <span style="color:#c0392b">*</span></label>
                        <select name="document_type" class="form-control" required>
                            @foreach(['MEMO','EO','SO','LETTER','SP','TO','OTHERS'] as $t)
                                <option value="{{ $t }}" {{ old('document_type', $document->document_type) === $t ? 'selected' : '' }}>{{ $t === 'TO' ? 'TO - Travel Order' : $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Communication Type <span style="color:#c0392b">*</span></label>
                        <select name="direction" class="form-control" required>
                            <option value="INCOMING" {{ old('direction', $document->direction) === 'INCOMING' ? 'selected' : '' }}>INCOMING</option>
                            <option value="OUTGOING" {{ old('direction', $document->direction) === 'OUTGOING' ? 'selected' : '' }}>OUTGOING</option>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:16px;">
                    <div class="form-group" id="delivery-scope-group" style="{{ old('direction', $document->direction) === 'OUTGOING' ? '' : 'display:none;' }}">
                        <label>Outgoing Type <span style="color:#c0392b">*</span></label>
                        <select name="delivery_scope" id="delivery_scope" class="form-control" {{ old('direction', $document->direction) === 'OUTGOING' ? 'required' : '' }}>
                            <option value="">Select Type</option>
                            <option value="EXTERNAL" {{ old('delivery_scope', $document->delivery_scope) === 'EXTERNAL' ? 'selected' : '' }}>External</option>
                            <option value="INTERNAL" {{ old('delivery_scope', $document->delivery_scope) === 'INTERNAL' ? 'selected' : '' }}>Internal</option>
                        </select>
                        <small style="color:#999;">External means sent to another office. Internal means sent within your office or unit structure.</small>
                    </div>
                    <div class="form-group">
                        <label>Originating Office <span id="originating-office-required" style="color:#c0392b;">*</span></label>
                        <select name="originating_office" id="originating_office" class="form-control" {{ old('direction', $document->direction) === 'OUTGOING' ? '' : 'required' }}>
                            <option value="">Select Office</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" {{ old('originating_office', $document->originating_office) == $office->id ? 'selected' : '' }}>{{ $office->code }} - {{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:16px;">
                    <div class="form-group">
                        <label>To (Destination Office)</label>
                        <select name="to_office" class="form-control">
                            <option value="">Select Office</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" {{ old('to_office', $document->to_office) == $office->id ? 'selected' : '' }}>{{ $office->code }} - {{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sub-Number <small style="color:#999;">(Optional)</small></label>
                        <input type="text" name="memorandum_number" class="form-control" value="{{ old('memorandum_number', $document->memorandum_number) }}" placeholder="Enter sub-number or office number">
                    </div>
                </div>

                <div id="travel-order-fields" style="{{ !empty($isTravelOrder) ? '' : 'display:none;' }}">
                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px;">
                        <div class="form-group">
                            <label>Travel Order Type <span style="color:#c0392b">*</span></label>
                            <select name="travel_order_type" id="travel_order_type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="WITHIN_LA_UNION" {{ old('travel_order_type', $document->travel_order_type) === 'WITHIN_LA_UNION' ? 'selected' : '' }}>Travel Order (within LU)</option>
                                <option value="OUTSIDE_LA_UNION" {{ old('travel_order_type', $document->travel_order_type) === 'OUTSIDE_LA_UNION' ? 'selected' : '' }}>Travel Order (outside LU)</option>
                                <option value="SPECIAL_ORDER" {{ old('travel_order_type', $document->travel_order_type) === 'SPECIAL_ORDER' ? 'selected' : '' }}>Special Order</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date/s of Travel</label>
                            <input type="text" name="travel_dates" class="form-control" value="{{ old('travel_dates', $document->travel_dates) }}" placeholder="e.g., January 6 to 7, 2025">
                        </div>
                        <div class="form-group">
                            <label>Destination/s</label>
                            <input type="text" name="destinations" class="form-control" value="{{ old('destinations', $document->destinations) }}" placeholder="Enter destination or destinations">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Name/s of Traveler/s</label>
                        <textarea name="travelers" class="form-control" rows="4" placeholder="Enter one or more names">{{ old('travelers', $document->travelers) }}</textarea>
                    </div>
                </div>

                <div class="form-group" id="document-subject-group" style="{{ !empty($isTravelOrder) ? 'display:none;' : '' }}">
                    <label>Subject / Title <span style="color:#c0392b">*</span></label>
                    <input type="text" name="subject" id="document-subject-input" class="form-control" value="{{ old('subject', $document->subject) }}" {{ !empty($isTravelOrder) ? '' : 'required' }}>
                </div>
                <input type="hidden" name="subject" id="travel-order-subject" value="{{ old('subject', $document->subject ?: 'Travel Order') }}" {{ !empty($isTravelOrder) ? '' : 'disabled' }}>

                <div id="letter-fields" style="{{ old('document_type', $document->document_type) === 'LETTER' ? '' : 'display:none;' }}">
                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px;">
                        <div class="form-group">
                            <label>OPG Reference No.</label>
                            <input type="text" name="opg_reference_no" class="form-control" value="{{ old('opg_reference_no', $document->opg_reference_no) }}" placeholder="Enter OPG Reference Number">
                        </div>
                        <div class="form-group">
                            <label>OPA Reference No.</label>
                            <input type="text" name="opa_reference_no" class="form-control" value="{{ old('opa_reference_no', $document->opa_reference_no) }}" placeholder="Enter OPA Reference Number">
                        </div>
                        <div class="form-group">
                            <label>Governor's Instruction</label>
                            <input type="text" name="governors_instruction" class="form-control" value="{{ old('governors_instruction', $document->governors_instruction) }}" placeholder="Enter Governor's Instruction">
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px;">
                        <div class="form-group">
                            <label>Administrator's Instruction</label>
                            <input type="text" name="administrators_instruction" class="form-control" value="{{ old('administrators_instruction', $document->administrators_instruction) }}" placeholder="Enter Administrator's Instruction">
                        </div>
                        <div class="form-group">
                            <label>Returned</label>
                            <input type="text" name="returned" class="form-control" value="{{ old('returned', $document->returned) }}" placeholder="Enter Returned Status">
                        </div>
                        <div class="form-group">
                            <label>OPG Action Slip</label>
                            <input type="text" name="opg_action_slip" class="form-control" value="{{ old('opg_action_slip', $document->opg_action_slip) }}" placeholder="Enter OPG Action Slip">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>DTS No.</label>
                        <input type="text" name="dts_no" class="form-control" value="{{ old('dts_no', $document->dts_no) }}" placeholder="Enter DTS Number">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:16px;">
                    <div class="form-group">
                        <label id="particulars-label">Particulars <small style="color:#999;">(Optional)</small></label>
                        <textarea name="particulars" class="form-control" rows="3" placeholder="Enter specific details or particulars about this document...">{{ old('particulars', $document->particulars) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Period <small style="color:#999;">(Optional)</small></label>
                        <input type="text" name="period" class="form-control" value="{{ old('period', $document->period) }}" placeholder="e.g., 1st Quarter, 2nd Semester, FY 2026">
                    </div>
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

                <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:16px;">
                    <div class="form-group">
                        <label>Date Received</label>
                        <input type="date" name="date_received" class="form-control" value="{{ old('date_received', $document->date_received?->format('Y-m-d')) }}">
                        <small style="color:#999;">Changing the date will regenerate Tracking Code and Transaction Number to match the new year.</small>
                    </div>
                    <div class="form-group">
                        <label>Received via Online?</label>
                        <select name="received_via_online" class="form-control">
                            <option value="0" {{ (string) old('received_via_online', $document->received_via_online ? '1' : '0') === '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ (string) old('received_via_online', $document->received_via_online ? '1' : '0') === '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:16px;">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            @foreach(['ONGOING','DELIVERED','DONE'] as $s)
                                <option value="{{ $s }}" {{ old('status', $document->status) === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $document->remarks) }}</textarea>
                    </div>
                </div>

                <div style="margin-top:8px; padding:16px; border-radius:14px; border:1px solid rgba(148,163,184,0.22); background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%);">
                    <div style="font-size:13px; font-weight:700; color:#334155; margin-bottom:12px;">Files & Google Drive</div>
                    <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:16px;">
                        <div class="form-group" style="margin:0;">
                            <label>Google Drive Link</label>
                            <input type="url" name="shared_drive_link" class="form-control" value="{{ old('shared_drive_link', $document->shared_drive_link) }}" placeholder="https://drive.google.com/...">
                            <small style="color:#64748b;">Use this when the file is referenced through Google Drive instead of only inside the local system.</small>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label>Add More Files (PDF, Docs, Sheets, Images)</label>
                            <input type="file" name="files[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif" class="form-control" style="padding:6px;">
                            <small style="color:#999;">Max 10MB per file. You can select multiple files.</small>
                        </div>
                    </div>
                </div>

                <div style="display:flex; gap:10px; margin-top:8px;">
                    <label style="display:flex; align-items:center; gap:8px; margin-right:auto; font-size:12px; color:#64748b;">
                        <input type="checkbox" name="force_save_duplicate" value="1" {{ old('force_save_duplicate') ? 'checked' : '' }}>
                        Save anyway if this matches an existing document
                    </label>
                    <button type="submit" class="btn-red"><i class="fas fa-save"></i> {{ !empty($isTravelOrder) ? 'Update Travel Order' : 'Update Document' }}</button>
                    <a href="{{ route('documents.show', $document) }}" class="btn-blue"><i class="fas fa-eye"></i> View</a>
                    <a href="{{ route('documents.index', !empty($isTravelOrder) ? ['type' => 'TO'] : []) }}" class="btn-gray"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const directionSelect = document.querySelector('select[name="direction"]');
    const originatingOfficeSelect = document.querySelector('select[name="originating_office"]');
    const requiredSpan = document.getElementById('originating-office-required');
    const documentTypeSelect = document.querySelector('select[name="document_type"]');
    const letterFields = document.getElementById('letter-fields');
    const deliveryScopeGroup = document.getElementById('delivery-scope-group');
    const deliveryScopeSelect = document.getElementById('delivery_scope');
    const travelOrderFields = document.getElementById('travel-order-fields');
    const travelOrderTypeSelect = document.getElementById('travel_order_type');
    const subjectGroup = document.getElementById('document-subject-group');
    const subjectInput = document.getElementById('document-subject-input');
    const hiddenTravelSubject = document.getElementById('travel-order-subject');
    const particularsLabel = document.getElementById('particulars-label');

    function toggleOriginatingOffice() {
        if (directionSelect.value === 'OUTGOING') {
            originatingOfficeSelect.removeAttribute('required');
            if (requiredSpan) requiredSpan.style.display = 'none';
        } else {
            originatingOfficeSelect.setAttribute('required', 'required');
            if (requiredSpan) requiredSpan.style.display = 'inline';
        }
    }

    function toggleDeliveryScope() {
        if (directionSelect.value === 'OUTGOING') {
            deliveryScopeGroup.style.display = 'block';
            deliveryScopeSelect.setAttribute('required', 'required');
        } else {
            deliveryScopeGroup.style.display = 'none';
            deliveryScopeSelect.removeAttribute('required');
            deliveryScopeSelect.value = '';
        }
    }

    function toggleTravelOrderFields() {
        const isTravelOrder = documentTypeSelect.value === 'TO';

        travelOrderFields.style.display = isTravelOrder ? 'block' : 'none';
        subjectGroup.style.display = isTravelOrder ? 'none' : 'block';

        if (isTravelOrder) {
            directionSelect.value = 'OUTGOING';
            directionSelect.style.pointerEvents = 'none';
            directionSelect.style.opacity = '0.7';
            deliveryScopeGroup.style.display = 'block';
            deliveryScopeSelect.value = 'INTERNAL';
            deliveryScopeSelect.setAttribute('required', 'required');
            subjectInput.removeAttribute('required');
            hiddenTravelSubject.disabled = false;
            hiddenTravelSubject.value = 'Travel Order';
            travelOrderTypeSelect.setAttribute('required', 'required');
            particularsLabel.innerHTML = 'Particulars / Purpose <span style="color:#c0392b">*</span>';
        } else {
            subjectInput.setAttribute('required', 'required');
            hiddenTravelSubject.disabled = true;
            directionSelect.style.pointerEvents = '';
            directionSelect.style.opacity = '';
            travelOrderTypeSelect.removeAttribute('required');
            particularsLabel.innerHTML = 'Particulars <small style="color:#999;">(Optional)</small>';
        }

        toggleOriginatingOffice();
        toggleDeliveryScope();
    }

    function toggleLetterFields() {
        if (documentTypeSelect.value === 'LETTER') {
            letterFields.style.display = 'block';
        } else {
            letterFields.style.display = 'none';
        }
    }

    directionSelect.addEventListener('change', toggleOriginatingOffice);
    directionSelect.addEventListener('change', toggleDeliveryScope);
    documentTypeSelect.addEventListener('change', toggleTravelOrderFields);
    documentTypeSelect.addEventListener('change', toggleLetterFields);

    toggleOriginatingOffice();
    toggleDeliveryScope();
    toggleTravelOrderFields();
    toggleLetterFields();
});
</script>
