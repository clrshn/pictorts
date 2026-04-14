<x-app-layout>
    <x-slot name="header">
        <h1>Add New Office</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / <a href="{{ route('offices.index') }}">Office Management</a> / Add New Office</div>
    </x-slot>

    <div class="filter-box office-search-box">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:16px; flex-wrap:wrap;">
            <h3 style="margin:0;">Existing Offices</h3>
            <span class="office-search-count">{{ $offices->count() }} saved office{{ $offices->count() === 1 ? '' : 's' }}</span>
        </div>

        <div class="office-search-field">
            <input type="text" id="officeLookup" class="form-control" placeholder="Search office code or name before creating a new one...">
        </div>

        <div class="office-search-results" id="officeLookupResults">
            @forelse($offices as $existingOffice)
                <div class="office-search-item" data-office-search="{{ strtolower($existingOffice->code . ' ' . $existingOffice->name) }}">
                    <strong>{{ $existingOffice->code }}</strong>
                    <span>{{ $existingOffice->name }}</span>
                </div>
            @empty
                <div class="office-search-empty">No offices have been added yet.</div>
            @endforelse
        </div>
    </div>

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

        .office-search-box {
            margin-bottom: 18px;
        }

        .office-search-field {
            margin-bottom: 14px;
        }

        .office-search-count {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
        }

        .office-search-results {
            display: grid;
            gap: 10px;
            max-height: 240px;
            overflow-y: auto;
        }

        .office-search-item {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 12px 14px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.96) 100%);
            border: 1px solid rgba(226,232,240,0.9);
        }

        .office-search-item strong {
            color: #1e293b;
            min-width: 72px;
        }

        .office-search-item span {
            color: #475569;
            flex: 1;
        }

        .office-search-empty {
            padding: 14px;
            border-radius: 12px;
            color: #64748b;
            text-align: center;
            background: rgba(248,250,252,0.8);
            border: 1px dashed rgba(148,163,184,0.35);
        }
    </style>

    <script>
        const officeLookup = document.getElementById('officeLookup');
        const officeItems = Array.from(document.querySelectorAll('.office-search-item'));

        if (officeLookup) {
            officeLookup.addEventListener('input', function () {
                const term = this.value.trim().toLowerCase();
                let visibleCount = 0;

                officeItems.forEach((item) => {
                    const matches = !term || item.dataset.officeSearch.includes(term);
                    item.style.display = matches ? 'flex' : 'none';

                    if (matches) {
                        visibleCount += 1;
                    }
                });
            });
        }
    </script>
</x-app-layout>
