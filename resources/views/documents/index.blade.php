<x-app-layout>
    <x-slot name="header">
        <h1>Documents</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / Documents</div>
    </x-slot>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <!-- Search Filter -->
    <div class="filter-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="margin:0;">Search Filter</h3>
            @if(request()->hasAny(['direction', 'status', 'type', 'month', 'year', 'search']))
                <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
                    <span style="font-weight:600; color:#666;">Active Filters:</span>
                    @if(request('direction'))
                        <span class="badge" style="background:#1976d2; color:white; padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                            Direction: {{ request('direction') }}
                            <a href="{{ request()->fullUrlWithQuery(['direction' => null]) }}" style="color:white; text-decoration:none; font-weight:bold; cursor:pointer;" title="Remove direction filter">×</a>
                        </span>
                    @endif
                    @if(request('status'))
                        <span class="badge" style="background:#1976d2; color:white; padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                            Status: {{ request('status') }}
                            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" style="color:white; text-decoration:none; font-weight:bold; cursor:pointer;" title="Remove status filter">×</a>
                        </span>
                    @endif
                    @if(request('type'))
                        <span class="badge" style="background:#1976d2; color:white; padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                            Type: {{ request('type') }}
                            <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" style="color:white; text-decoration:none; font-weight:bold; cursor:pointer;" title="Remove type filter">×</a>
                        </span>
                    @endif
                    @if(request('month'))
                        <span class="badge" style="background:#1976d2; color:white; padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                            Month: {{ request('month') }}
                            <a href="{{ request()->fullUrlWithQuery(['month' => null]) }}" style="color:white; text-decoration:none; font-weight:bold; cursor:pointer;" title="Remove month filter">×</a>
                        </span>
                    @endif
                    @if(request('year'))
                        <span class="badge" style="background:#1976d2; color:white; padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                            Year: {{ request('year') }}
                            <a href="{{ request()->fullUrlWithQuery(['year' => null]) }}" style="color:white; text-decoration:none; font-weight:bold; cursor:pointer;" title="Remove year filter">×</a>
                        </span>
                    @endif
                    @if(request('search'))
                        <span class="badge" style="background:#1976d2; color:white; padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                            Search: {{ request('search') }}
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" style="color:white; text-decoration:none; font-weight:bold; cursor:pointer;" title="Remove search filter">×</a>
                        </span>
                    @endif
                    <a href="{{ route('documents.index') }}" style="color:#1976d2; cursor:pointer; font-weight:600; text-decoration:underline;">Clear All</a>
                </div>
            @endif
        </div>
        <form method="GET" action="{{ route('documents.index') }}">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:12px;">
                <div class="form-group" style="margin:0">
                    <label>Month</label>
                    <select name="month" class="form-control">
                        <option value="">All Months</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group" style="margin:0">
                    <label>Year</label>
                    <input type="number" name="year" class="form-control" value="{{ request('year', now()->year) }}" min="2020" max="2030">
                </div>
                <div class="form-group" style="margin:0">
                    <label>Document Type</label>
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        @foreach(['MEMO','EO','SO','LETTER','SP','OTHERS'] as $t)
                            <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin:0">
                    <label>Direction</label>
                    <select name="direction" class="form-control">
                        <option value="">All</option>
                        <option value="INCOMING" {{ request('direction') === 'INCOMING' ? 'selected' : '' }}>Incoming</option>
                        <option value="OUTGOING" {{ request('direction') === 'OUTGOING' ? 'selected' : '' }}>Outgoing</option>
                    </select>
                </div>
                <div class="form-group" style="margin:0; display:flex; align-items:flex-end; gap:8px;">
                    <button type="submit" class="btn-red"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('documents.index') }}" class="btn-gray">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Documents Table -->
    <div class="table-card">
        <div class="table-header">
            <h3>Documents Table</h3>
            <div style="display:flex; gap:10px; align-items:center;">
                <form method="GET" action="{{ route('documents.index') }}" style="display:flex; gap:8px;">
                    @foreach(request()->except('search','page') as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <input type="text" name="search" class="search-input" placeholder="Search Keywords Here" value="{{ request('search') }}" style="width:220px;">
                </form>
                <a href="{{ route('documents.create') }}" class="btn-red"><i class="fas fa-plus"></i> Add New Document</a>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>SEQ #</th>
                        <th>ACTION</th>
                        <th>TRACKING CODE</th>
                        <th>TRANSACTION NO</th>
                        <th>SUBJECT</th>
                        <th>DOCUMENT TYPE</th>
                        <th>ORIGINATING OFFICE</th>
                        <th>STATUS</th>
                        <th>DOCUMENT DATE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $index => $doc)
                        <tr>
                            <td>{{ $documents->firstItem() + $index }}</td>
                            <td>
                                <a href="{{ route('documents.show', $doc) }}" class="btn-green" title="View Document & Tracking"><i class="fas fa-route"></i></a>
                                <a href="{{ route('documents.edit', $doc) }}" class="btn-blue" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('documents.destroy', $doc) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this document?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                            <td style="font-family:monospace; font-size:12px;">{{ $doc->dts_number }}</td>
                            <td style="font-family:monospace; font-size:12px;">{{ $doc->doc_number ?? '—' }}</td>
                            <td style="max-width:280px; text-align:left;">{{ $doc->subject }}</td>
                            <td>{{ $doc->document_type }}</td>
                            <td>{{ $doc->originatingOffice->code ?? '—' }}</td>
                            <td>
                                @php
                                    $badgeClass = match($doc->status) {
                                        'ONGOING' => 'badge-ongoing',
                                        'DELIVERED' => 'badge-delivered',
                                        'COMPLETED' => 'badge-completed',
                                        default => ''
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $doc->status }}</span>
                            </td>
                            <td>{{ $doc->date_received ? $doc->date_received->format('F d, Y') : ($doc->created_at ? $doc->created_at->format('F d, Y') : '—') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding:30px; color:#999;">No documents found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding:16px 20px; display:flex; justify-content:center;">
            {{ $documents->links() }}
        </div>
    </div>
</x-app-layout>
