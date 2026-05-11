<x-app-layout>
    <x-slot name="header">
        <h1>Edit Financial Record</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('financial.index') }}">Financial</a> / Edit</div>
    </x-slot>

    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px;">
            <i class="fas fa-edit"></i> EDIT FINANCIAL RECORD #{{ $financial->id }}
        </div>
        @include('financial._form', [
            'offices' => $offices,
            'financialRecord' => $financial,
            'formMode' => 'edit',
        ])
    </div>
</x-app-layout>
