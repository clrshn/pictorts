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

    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px;">
            <i class="fas fa-edit"></i> EDIT FINANCIAL RECORD #{{ $financial->id }}
        </div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('financial.update', $financial) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Type</label>
                        <input type="text" name="type" class="form-control" value="{{ old('type', $financial->type) }}">
                    </div>
                    <div class="form-group">
                        <label>Supplier</label>
                        <input type="text" name="supplier" class="form-control" value="{{ old('supplier', $financial->supplier) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Description <span style="color:#c0392b">*</span></label>
                    <textarea name="description" class="form-control" rows="2" required placeholder="Enter description of this financial transaction">{{ old('description', $financial->description) }}</textarea>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Status <span style="color:#c0392b">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="ACTIVE" {{ old('status', $financial->status) === 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                            <option value="CANCELLED" {{ old('status', $financial->status) === 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                            <option value="FINISHED" {{ old('status', $financial->status) === 'FINISHED' ? 'selected' : '' }}>FINISHED</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <!-- Empty for balance -->
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

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Office <span style="color:#c0392b">*</span></label>
                        <select name="office_origin" class="form-control" required>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" {{ $financial->office_origin == $office->id ? 'selected' : '' }}>{{ $office->code }} – {{ $office->name }}</option>
                            @endforeach
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
                        <label>Progress <small style="color:#999;">(Optional)</small></label>
                        <input type="text" name="progress" class="form-control" value="{{ old('progress', $financial->progress) }}" placeholder="e.g., For Processing, Under Review, Awaiting Approval">
                    </div>
                    <div class="form-group">
                        <!-- Empty for balance -->
                    </div>
                </div>

                <div class="form-group">
                    <label>Remarks / Notes</label>
                    <textarea name="remarks" class="form-control" rows="3" style="white-space: pre-wrap; wrap: soft;" placeholder="Enter remarks with proper formatting...">{{ old('remarks', $financial->remarks) }}</textarea>
                </div>

                <div class="form-group">
                    <label>Add More Files</label>
                    <input type="file" name="files[]" multiple accept=".pdf,.jpg,.jpeg,.png,.gif" class="form-control" style="padding:6px;">
                </div>

                <div style="display:flex; gap:10px; margin-top:8px;">
                    <button type="submit" class="btn-red"><i class="fas fa-save"></i> Update Record</button>
                    <a href="{{ route('financial.show', $financial) }}" class="btn-blue"><i class="fas fa-eye"></i> View</a>
                    <a href="{{ route('financial.index') }}" class="btn-gray"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
