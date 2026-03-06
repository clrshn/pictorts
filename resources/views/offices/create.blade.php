<x-app-layout>
    <x-slot name="header">
        <h1>Add New Office</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('offices.index') }}">Office Management</a> / Add New Office</div>
    </x-slot>

    <div class="table-card">
        <div class="table-header">
            <h3>Create New Office</h3>
        </div>

        <form method="POST" action="{{ route('offices.store') }}" style="padding:20px;">
            @csrf
            
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px,1fr)); gap:20px;">
                <div class="form-group">
                    <label for="code">Office Code *</label>
                    <input type="text" id="code" name="code" class="form-control" value="{{ old('code') }}" required maxlength="10" placeholder="e.g., OPG" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                    @error('code')
                        <div style="color:#e74c3c; font-size:12px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name">Office Name *</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g., Office of the Provincial Governor" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-size:14px;">
                    @error('name')
                        <div style="color:#e74c3c; font-size:12px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="margin-top:30px; display:flex; gap:10px; justify-content:flex-end;">
                <a href="{{ route('offices.index') }}" class="btn-gray">Cancel</a>
                <button type="submit" class="btn-red"><i class="fas fa-save"></i> Create Office</button>
            </div>
        </form>
    </div>

    <style>
        .form-group label {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            display: block;
            margin-bottom: 6px;
        }

        .form-control:focus {
            border-color: #c0392b;
            outline: none;
            box-shadow: 0 0 0 2px rgba(192, 57, 43, 0.2);
        }

        .table-card {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
        }

        .table-header h3 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .btn-red, .btn-blue, .btn-green, .btn-gray, .btn-danger {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</x-app-layout>
