<x-app-layout>
    <x-slot name="header">
        <h1>{{ !empty($isTravelOrder) ? 'Edit Travel Order' : 'Edit Document' }}</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('documents.index', !empty($isTravelOrder) ? ['type' => 'TO'] : []) }}">{{ !empty($isTravelOrder) ? 'Travel Orders' : 'Documents' }}</a> / Edit</div>
    </x-slot>

    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px;">
            <i class="fas fa-edit"></i> EDIT DOCUMENT - {{ $document->dts_number }}
        </div>

        @include('documents._form', [
            'document' => $document,
            'offices' => $offices,
            'formMode' => 'edit',
            'isTravelOrder' => $isTravelOrder,
            'returnUrl' => request('return_to', route('documents.show', $document)),
            'cancelUrl' => route('documents.index', !empty($isTravelOrder) ? ['type' => 'TO'] : []),
            'formKey' => 'document-edit-page-' . $document->id,
        ])
    </div>
</x-app-layout>
