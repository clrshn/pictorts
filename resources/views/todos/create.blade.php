<x-app-layout>
    <x-slot name="header">
        <h1>Create Todo</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('todos.index') }}">Todo List</a> / Create Todo</div>
    </x-slot>



    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px;">
            <i class="fas fa-plus"></i> CREATE NEW TODO
        </div>
        @include('todos._form', [
            'assignedToOptions' => $assignedToOptions,
            'formMode' => 'create',
        ])
    </div>
</x-app-layout>
