@php
    $financialRecord = $financialRecord ?? null;
    $formMode = $formMode ?? ($financialRecord ? 'edit' : 'create');
    $isEdit = $formMode === 'edit';
    $isModal = $isModal ?? false;
    $formAction = $formAction ?? ($isEdit ? route('financial.update', $financialRecord) : route('financial.store'));
    $cancelUrl = $cancelUrl ?? route('financial.index');
    $modalId = $modalId ?? null;
    $returnUrl = $returnUrl ?? ($isModal
        ? request()->fullUrl()
        : ($isEdit ? route('financial.show', $financialRecord) : route('financial.index')));
    $helperText = $helperText ?? ($isEdit
        ? 'Reference code: ' . ($financialRecord->reference_code ?? 'Will be generated automatically if missing')
        : 'Reference code will be auto-generated upon saving, using the format PICTO-FIN-YEAR-SEQUENCE.');
@endphp

@if($errors->any())
    <div class="alert-error" style="margin-bottom:16px;">
        <ul style="margin:0; padding-left:18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('duplicate_warning'))
    <div class="alert alert-warning" style="margin-bottom:16px;">
        <strong>{{ session('duplicate_warning') }}</strong>
        @if(session('duplicate_candidates'))
            <ul style="margin:10px 0 0 18px; padding:0;">
                @foreach(session('duplicate_candidates') as $candidate)
                    <li>{{ $candidate->reference_code ?? 'No Ref' }} - {{ $candidate->description }} ({{ $candidate->supplier ?? 'No Supplier' }})</li>
                @endforeach
            </ul>
        @endif
    </div>
@endif

<div style="padding:24px;">
    <p style="font-size:12px; color:#64748b; margin-bottom:16px;">{{ $helperText }}</p>

    <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <input type="hidden" name="modal_mode" value="{{ $formMode }}">
        <input type="hidden" name="modal_record_id" value="{{ $financialRecord?->id }}">
        <input type="hidden" name="return_to" value="{{ $returnUrl }}">

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <div class="form-group">
                <label>Type</label>
                <select name="type" class="form-control">
                    <option value="">Select Type</option>
                    @foreach(['DV', 'INSPEC', 'LIQUIDATION', 'OBR', 'POST INSPECTION', 'PAYROLL', 'OPG', 'PR', 'PR,PO'] as $typeOption)
                        @php
                            $currentType = old('type', $financialRecord?->type);
                            $selected = $currentType === $typeOption
                                || ($typeOption === 'LIQUIDATION' && $currentType === 'LIQUADATION');
                        @endphp
                        <option value="{{ $typeOption }}" {{ $selected ? 'selected' : '' }}>{{ $typeOption }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Status <span style="color:#c0392b">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="">Select Status</option>
                    @foreach(['ACTIVE', 'CANCELLED', 'FINISHED'] as $statusOption)
                        <option value="{{ $statusOption }}" {{ old('status', $financialRecord?->status) === $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <div class="form-group">
                <label>Description <span style="color:#c0392b">*</span></label>
                <textarea name="description" class="form-control" rows="2" required placeholder="Enter description of this financial transaction">{{ old('description', $financialRecord?->description) }}</textarea>
            </div>
            <div class="form-group">
                <label>Supplier</label>
                <input type="text" name="supplier" class="form-control" value="{{ old('supplier', $financialRecord?->supplier) }}">
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <div class="form-group">
                <label>PR Number</label>
                <input type="text" name="pr_number" class="form-control" value="{{ old('pr_number', $financialRecord?->pr_number) }}" placeholder="Purchase Request No.">
            </div>
            <div class="form-group">
                <label>PR Amount</label>
                <input type="number" step="0.01" name="pr_amount" class="form-control" value="{{ old('pr_amount', $financialRecord?->pr_amount) }}" placeholder="0.00">
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <div class="form-group">
                <label>PO Number</label>
                <input type="text" name="po_number" class="form-control" value="{{ old('po_number', $financialRecord?->po_number) }}" placeholder="Purchase Order No.">
            </div>
            <div class="form-group">
                <label>PO Amount</label>
                <input type="number" step="0.01" name="po_amount" class="form-control" value="{{ old('po_amount', $financialRecord?->po_amount) }}" placeholder="0.00">
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <div class="form-group">
                <label>OBR Number</label>
                <input type="text" name="obr_number" class="form-control" value="{{ old('obr_number', $financialRecord?->obr_number) }}" placeholder="Obligation Request No.">
            </div>
            <div class="form-group">
                <label>Voucher Number</label>
                <input type="text" name="voucher_number" class="form-control" value="{{ old('voucher_number', $financialRecord?->voucher_number) }}">
            </div>
        </div>

        <div class="form-group">
            <label>Office <span style="color:#c0392b">*</span></label>
            <select name="office_origin" class="form-control" required>
                <option value="">Select Office</option>
                @foreach($offices as $office)
                    <option value="{{ $office->id }}" {{ (string) old('office_origin', $financialRecord?->office_origin) === (string) $office->id ? 'selected' : '' }}>{{ $office->code }} - {{ $office->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Progress / Remarks <small style="color:#999;">(Optional)</small></label>
            <textarea name="progress_remarks" class="form-control" rows="3" style="white-space: pre-wrap; wrap: soft;" placeholder="Enter progress updates or remarks...">{{ old('progress_remarks', old('remarks', $financialRecord?->remarks ?: $financialRecord?->progress)) }}</textarea>
        </div>

        <div class="form-group">
            <label>{{ $isEdit ? 'Add More Files' : 'Attach Files' }}</label>
            <input type="file" name="files[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif" class="form-control" style="padding:6px;">
        </div>

        <div style="display:flex; gap:10px; margin-top:8px; align-items:center; flex-wrap:wrap;">
            <label style="display:flex; align-items:center; gap:8px; margin-right:auto; font-size:12px; color:#64748b;">
                <input type="checkbox" name="force_save_duplicate" value="1" {{ old('force_save_duplicate') ? 'checked' : '' }}>
                Save anyway if this matches an existing financial record
            </label>
            <button type="submit" class="btn-red"><i class="fas fa-save"></i> {{ $isEdit ? 'Update Record' : 'Save Record' }}</button>
            @if($isModal && $modalId)
                <button type="button" class="btn-gray" onclick="closeFinancialFormModal('{{ $modalId }}')"><i class="fas fa-times"></i> Close</button>
            @else
                @if($isEdit)
                    <a href="{{ route('financial.show', $financialRecord) }}" class="btn-blue"><i class="fas fa-eye"></i> View</a>
                @endif
                <a href="{{ $cancelUrl }}" class="btn-gray"><i class="fas fa-times"></i> Cancel</a>
            @endif
        </div>
    </form>
</div>
