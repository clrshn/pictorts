<x-app-layout>
    <x-slot name="header">
        <h1>Edit Financial Record</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('financial.index') }}">Financial</a> / Edit</div>
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
                        <li>{{ $candidate->reference_code ?? 'No Ref' }} - {{ $candidate->description }} ({{ $candidate->supplier ?? 'No Supplier' }})</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px;">
            <i class="fas fa-edit"></i> EDIT FINANCIAL RECORD #{{ $financial->id }}
        </div>
        <div style="padding:24px;">
            <p style="font-size:12px; color:#999; margin-bottom:16px;">Reference code: <strong>{{ $financial->reference_code ?? 'Will be generated automatically if missing' }}</strong></p>
            <form method="POST" action="{{ route('financial.update', $financial) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-control">
                            <option value="">Select Type</option>
                            <option value="DV" {{ old('type', $financial->type) === 'DV' ? 'selected' : '' }}>DV</option>
                            <option value="INSPEC" {{ old('type', $financial->type) === 'INSPEC' ? 'selected' : '' }}>INSPEC</option>
                            <option value="LIQUIDATION" {{ old('type', $financial->type) === 'LIQUIDATION' || old('type', $financial->type) === 'LIQUADATION' ? 'selected' : '' }}>LIQUIDATION</option>
                            <option value="OBR" {{ old('type', $financial->type) === 'OBR' ? 'selected' : '' }}>OBR</option>
                            <option value="POST INSPECTION" {{ old('type', $financial->type) === 'POST INSPECTION' ? 'selected' : '' }}>POST INSPECTION</option>
                            <option value="PAYROLL" {{ old('type', $financial->type) === 'PAYROLL' ? 'selected' : '' }}>PAYROLL</option>
                            <option value="OPG" {{ old('type', $financial->type) === 'OPG' ? 'selected' : '' }}>OPG</option>
                            <option value="PR" {{ old('type', $financial->type) === 'PR' ? 'selected' : '' }}>PR</option>
                            <option value="PR,PO" {{ old('type', $financial->type) === 'PR,PO' ? 'selected' : '' }}>PR,PO</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status <span style="color:#c0392b">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="ACTIVE" {{ old('status', $financial->status) === 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                            <option value="CANCELLED" {{ old('status', $financial->status) === 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                            <option value="FINISHED" {{ old('status', $financial->status) === 'FINISHED' ? 'selected' : '' }}>FINISHED</option>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Description <span style="color:#c0392b">*</span></label>
                        <textarea name="description" class="form-control" rows="2" required placeholder="Enter description of this financial transaction">{{ old('description', $financial->description) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Supplier</label>
                        <input type="text" name="supplier" class="form-control" value="{{ old('supplier', $financial->supplier) }}">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>PR Number</label>
                        <input type="text" name="pr_number" class="form-control" value="{{ old('pr_number', $financial->pr_number) }}">
                    </div>
                    <div class="form-group">
                        <label>PR Amount</label>
                        <input type="number" step="0.01" name="pr_amount" class="form-control" value="{{ old('pr_amount', $financial->pr_amount) }}">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>PO Number</label>
                        <input type="text" name="po_number" class="form-control" value="{{ old('po_number', $financial->po_number) }}">
                    </div>
                    <div class="form-group">
                        <label>PO Amount</label>
                        <input type="number" step="0.01" name="po_amount" class="form-control" value="{{ old('po_amount', $financial->po_amount) }}">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>OBR Number</label>
                        <input type="text" name="obr_number" class="form-control" value="{{ old('obr_number', $financial->obr_number) }}">
                    </div>
                    <div class="form-group">
                        <label>Voucher Number</label>
                        <input type="text" name="voucher_number" class="form-control" value="{{ old('voucher_number', $financial->voucher_number) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Office <span style="color:#c0392b">*</span></label>
                    <select name="office_origin" class="form-control" required>
                        <option value="">Select Office</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}" {{ old('office_origin', $financial->office_origin) == $office->id ? 'selected' : '' }}>{{ $office->code }} - {{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Progress <small style="color:#999;">(Optional)</small></label>
                        <input type="text" name="progress" class="form-control" value="{{ old('progress', $financial->progress) }}" placeholder="e.g., For Processing, Under Review, Awaiting Approval">
                    </div>
                    <div class="form-group">
                        <label>Remarks / Notes</label>
                        <textarea name="remarks" class="form-control" rows="3" style="white-space: pre-wrap; wrap: soft;" placeholder="Enter remarks with proper formatting...">{{ old('remarks', $financial->remarks) }}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label>Add More Files</label>
                    <input type="file" name="files[]" multiple accept=".pdf,.jpg,.jpeg,.png,.gif" class="form-control" style="padding:6px;">
                </div>

                <div style="display:flex; gap:10px; margin-top:8px;">
                    <label style="display:flex; align-items:center; gap:8px; margin-right:auto; font-size:12px; color:#64748b;">
                        <input type="checkbox" name="force_save_duplicate" value="1" {{ old('force_save_duplicate') ? 'checked' : '' }}>
                        Save anyway if this matches an existing financial record
                    </label>
                    <button type="submit" class="btn-red"><i class="fas fa-save"></i> Update Record</button>
                    <a href="{{ route('financial.show', $financial) }}" class="btn-blue"><i class="fas fa-eye"></i> View</a>
                    <a href="{{ route('financial.index') }}" class="btn-gray"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
