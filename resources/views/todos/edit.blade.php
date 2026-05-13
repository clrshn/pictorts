<x-app-layout>
    <x-slot name="header">
        <h1>Edit Todo</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('todos.index') }}">Todo List</a> / Edit Todo</div>
    </x-slot>



    <div class="table-card">
        <div style="background:#8b0000; color:#fff; padding:12px 20px; font-weight:600; font-size:14px;">
            <i class="fas fa-edit"></i> EDIT TODO
        </div>
        @include('todos._form', [
            'todo' => $todo,
            'assignedToOptions' => $assignedToOptions,
            'formMode' => 'edit',
        ])
    </div>
</x-app-layout>
