<x-app-layout>
    <x-slot name="header">
        <h1>{{ !empty($isTravelOrder) ? 'Add Travel Order' : 'Add New Document' }}</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('documents.index', !empty($isTravelOrder) ? ['type' => 'TO'] : []) }}">{{ !empty($isTravelOrder) ? 'Travel Orders' : 'Documents' }}</a> / Add New</div>
    </x-slot>

    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px;">
            <i class="fas fa-file-alt"></i> {{ !empty($isTravelOrder) ? 'TRAVEL ORDER ENCODING FORM' : 'DOCUMENT ENCODING FORM' }}
        </div>

        @include('documents._form', [
            'offices' => $offices,
            'formMode' => 'create',
            'isTravelOrder' => $isTravelOrder,
            'returnUrl' => request('return_to', route('documents.index', !empty($isTravelOrder) ? ['type' => 'TO'] : [])),
            'cancelUrl' => route('documents.index', !empty($isTravelOrder) ? ['type' => 'TO'] : []),
            'formKey' => 'document-create-page',
        ])
    </div>
</x-app-layout>
