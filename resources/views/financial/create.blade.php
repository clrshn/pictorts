<x-app-layout>
    <x-slot name="header">
        <h1>Add New Financial Record</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('financial.index') }}">Financial</a> / Add New</div>
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
            <i class="fas fa-coins"></i> FINANCIAL RECORD FORM
        </div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('financial.store') }}" enctype="multipart/form-data">
                @csrf

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>Type</label>
                        <input type="text" name="type" class="form-control" value="{{ old('type') }}" placeholder="e.g. Supplies, Equipment, Services">
                    </div>
                    <div class="form-group">
                        <label>Supplier</label>
                        <input type="text" name="supplier" class="form-control" value="{{ old('supplier') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Subject/Title <span style="color:#c0392b">*</span></label>
                    <textarea name="description" class="form-control" rows="2" required placeholder="Enter subject or title of this financial transaction">{{ old('description') }}</textarea>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>PR Number</label>
                        <input type="text" name="pr_number" class="form-control" value="{{ old('pr_number') }}" placeholder="Purchase Request No.">
                    </div>
                    <div class="form-group">
                        <label>PR Amount</label>
                        <input type="number" step="0.01" name="pr_amount" class="form-control" value="{{ old('pr_amount') }}" placeholder="0.00">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>PO Number</label>
                        <input type="text" name="po_number" class="form-control" value="{{ old('po_number') }}" placeholder="Purchase Order No.">
                    </div>
                    <div class="form-group">
                        <label>PO Amount</label>
                        <input type="number" step="0.01" name="po_amount" class="form-control" value="{{ old('po_amount') }}" placeholder="0.00">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label>OBR Number</label>
                        <input type="text" name="obr_number" class="form-control" value="{{ old('obr_number') }}" placeholder="Obligation Request No.">
                    </div>
                    <div class="form-group">
                        <label>Voucher Number</label>
                        <input type="text" name="voucher_number" class="form-control" value="{{ old('voucher_number') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Office <span style="color:#c0392b">*</span></label>
                    <select name="office_origin" class="form-control" required>
                        <option value="">Select Office</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}" {{ old('office_origin') == $office->id ? 'selected' : '' }}>{{ $office->code }} – {{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Remarks / Notes</label>
                    <textarea name="remarks" class="form-control" rows="2">{{ old('remarks') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Attach Files</label>
                    <input type="file" name="files[]" multiple accept=".pdf,.jpg,.jpeg,.png,.gif" class="form-control" style="padding:6px;">
                </div>

                <div style="display:flex; gap:10px; margin-top:8px;">
                    <button type="submit" class="btn-red"><i class="fas fa-save"></i> Save Record</button>
                    <a href="{{ route('financial.index') }}" class="btn-gray"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
