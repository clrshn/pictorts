<x-app-layout>
    <x-slot name="header">
        <h1>Financial Monitoring</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / Financial</div>
    </x-slot>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <!-- Search Filter -->
    <div class="filter-box">
        <h3>Search Filter</h3>
        <form method="GET" action="{{ route('financial.index') }}">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:12px;">
                <div class="form-group" style="margin:0">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                        <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                        <option value="FINISHED" {{ request('status') === 'FINISHED' ? 'selected' : '' }}>Finished</option>
                    </select>
                </div>
                <div class="form-group" style="margin:0">
                    <label>Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="PR, PO, supplier...">
                </div>
                <div class="form-group" style="margin:0; display:flex; align-items:flex-end; gap:8px;">
                    <button type="submit" class="btn-red"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('financial.index') }}" class="btn-gray">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Financial Table -->
    <div class="table-card">
        <div class="table-header">
            <h3>Financial Records Table</h3>
            <a href="{{ route('financial.create') }}" class="btn-red"><i class="fas fa-plus"></i> Add New Record</a>
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>SEQ #</th>
                        <th>ACTION</th>
                        <th>TYPE</th>
                        <th>DESCRIPTION</th>
                        <th>SUPPLIER</th>
                        <th>PR NO.</th>
                        <th>PR AMT</th>
                        <th>PO NO.</th>
                        <th>OBR NO.</th>
                        <th>OFFICE</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $index => $rec)
                        <tr>
                            <td>{{ $records->firstItem() + $index }}</td>
                            <td>
                                <a href="{{ route('financial.edit', $rec) }}" class="btn-blue" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('financial.destroy', $rec) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                            <td>{{ $rec->type ?? '—' }}</td>
                            <td style="max-width:220px; text-align:left;">{{ $rec->description }}</td>
                            <td>{{ $rec->supplier ?? '—' }}</td>
                            <td style="font-family:monospace; font-size:12px;">{{ $rec->pr_number ?? '—' }}</td>
                            <td>{{ $rec->pr_amount ? number_format($rec->pr_amount, 2) : '—' }}</td>
                            <td style="font-family:monospace; font-size:12px;">{{ $rec->po_number ?? '—' }}</td>
                            <td style="font-family:monospace; font-size:12px;">{{ $rec->obr_number ?? '—' }}</td>
                            <td>{{ $rec->originOffice->code ?? '—' }}</td>
                            <td>
                                @php
                                    $badgeClass = match($rec->status) {
                                        'ACTIVE' => 'badge-active',
                                        'CANCELLED' => 'badge-cancelled',
                                        'FINISHED' => 'badge-finished',
                                        default => ''
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $rec->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="padding:30px; color:#999;">No financial records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding:16px 20px; display:flex; justify-content:center;">
            {{ $records->links() }}
        </div>
    </div>
</x-app-layout>
