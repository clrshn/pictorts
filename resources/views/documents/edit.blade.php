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
                            @foreach(['MEMO','EO','SO','LETTER','SP','TO','OTHERS'] as $t)
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
                    <div class="form-group" id="delivery-scope-group" style="{{ $document->direction === 'OUTGOING' ? '' : 'display:none;' }}">
                        <label>Outgoing Type <span style="color:#c0392b">*</span></label>
                        <select name="delivery_scope" id="delivery_scope" class="form-control" {{ $document->direction === 'OUTGOING' ? 'required' : '' }}>
                            <option value="">Select Type</option>
                            <option value="EXTERNAL" {{ old('delivery_scope', $document->delivery_scope) === 'EXTERNAL' ? 'selected' : '' }}>External</option>
                            <option value="INTERNAL" {{ old('delivery_scope', $document->delivery_scope) === 'INTERNAL' ? 'selected' : '' }}>Internal</option>
                        </select>
                        <small style="color:#999;">External means sent to another office. Internal means sent within your office or unit structure.</small>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:16px;">
                    <div class="form-group">
                        <label>Originating Office <span id="originating-office-required" style="color:#c0392b;">*</span></label>
                        <select name="originating_office" id="originating_office" class="form-control" {{ $document->direction === 'OUTGOING' ? '' : 'required' }}>
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
                                <option value="{{ $office->id }}" {{ $document->to_office == $office->id ? 'selected' : '' }}>{{ $office->code }} - {{ $office->name }}</option>
                            @endforeach
                        </select>
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
                <input type="hidden" name="subject" id="travel-order-subject" value="{{ old('subject', $document->subject) }}" {{ !empty($isTravelOrder) ? '' : 'disabled' }}>

                <!-- Number (Optional) -->
                <div class="form-group">
                    <label>Number <small style="color:#999;">(Optional - For MEMO documents and special numbers)</small></label>
                    <input type="text" name="memorandum_number" class="form-control" value="{{ old('memorandum_number', $document->memorandum_number) }}" placeholder="e.g., MEMO-2026-001 or special number">
                </div>

                <!-- Period (Optional) -->
                <div class="form-group">
                    <label>Period <small style="color:#999;">(Optional)</small></label>
                    <input type="text" name="period" class="form-control" value="{{ old('period', $document->period) }}" placeholder="e.g., 1st Quarter, 2nd Semester, FY 2026">
                </div>

                <!-- Particulars (Optional) -->
                <div class="form-group">
                    <label id="particulars-label">Particulars <small style="color:#999;">(Optional)</small></label>
                    <textarea name="particulars" class="form-control" rows="3" placeholder="Enter specific details or particulars about this document...">{{ old('particulars', $document->particulars) }}</textarea>
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
                        <small style="color:#999;">⚠️ Changing the date will regenerate Tracking Code and Transaction Number to match the new year</small>
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
                            @foreach(['ONGOING','DELIVERED','DONE'] as $s)
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
            if (requiredSpan) {
                requiredSpan.style.display = 'none';
            }
        } else {
            originatingOfficeSelect.setAttribute('required', 'required');
            if (requiredSpan) {
                requiredSpan.style.display = 'inline';
            }
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
    
    directionSelect.addEventListener('change', toggleOriginatingOffice);
    directionSelect.addEventListener('change', toggleDeliveryScope);
    documentTypeSelect.addEventListener('change', toggleTravelOrderFields);
    toggleOriginatingOffice(); // Initialize on page load
    toggleDeliveryScope(); // Initialize on page load
    toggleTravelOrderFields(); // Initialize on page load
});
</script>
