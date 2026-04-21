<x-app-layout>
    <x-slot name="header">
        <h1>Financial Monitoring</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / Financial Monitoring</div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 16px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 16px;">
            {{ session('error') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning" style="margin-bottom: 16px;">
            {{ session('warning') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info" style="margin-bottom: 16px;">
            {{ session('info') }}
        </div>
    @endif

    @include('components.saved-filter-bar', [
        'module' => 'financial',
        'savedFilters' => $savedFilters ?? collect(),
    ])

    <!-- Search Filter -->
    <div class="filter-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="margin:0;">Search Filter</h3>
            @if(request()->hasAny(['status', 'type', 'search', 'sort_by']))
                <div class="active-filter-list" style="justify-content:flex-end;">
                    <span class="active-filter-label">Active Filters:</span>
                    @if(request('status'))
                        <span class="active-filter-pill">
                            {{ request('status') }}
                            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove status filter">×</a>
                        </span>
                    @endif
                    @if(request('type'))
                        <span class="active-filter-pill">
                            {{ request('type') }}
                            <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove type filter">×</a>
                        </span>
                    @endif
                    @if(request('search'))
                        <span class="active-filter-pill">
                            {{ request('search') }}
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove search filter">×</a>
                        </span>
                    @endif
                    @if(request('sort_by'))
                        <span class="active-filter-pill">
                            {{ match(request('sort_by')) {
                                'newest' => 'NEWEST TO OLDEST',
                                'oldest' => 'OLDEST TO NEWEST',
                                'az', 'description_az' => 'DESCRIPTION: A-Z',
                                'za', 'description_za' => 'DESCRIPTION: Z-A',
                                'highest', 'pr_highest' => 'PR AMOUNT: HIGHEST TO LOWEST',
                                'lowest', 'pr_lowest' => 'PR AMOUNT: LOWEST TO HIGHEST',
                                'po_highest' => 'PO AMOUNT: HIGHEST TO LOWEST',
                                'po_lowest' => 'PO AMOUNT: LOWEST TO HIGHEST',
                                default => strtoupper(str_replace('_', ' ', request('sort_by')))
                            } }}
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove sort filter">×</a>
                        </span>
                    @endif
                </div>
            @endif
        </div>
        
        <form method="GET" action="{{ route('financial.index') }}">
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('sort_by'))
                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            @endif
            <div style="display:grid; grid-template-columns: 1fr; gap:8px;">
                <div class="form-group" style="margin:0">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Enter keywords...">
                </div>
            </div>
            <div class="form-group" style="display:flex; gap:12px; margin-top:24px; justify-content:flex-end;">
                <button type="submit" class="btn-red" style="min-width: 100px; height: 36px; display: inline-flex; align-items: center; justify-content: center; vertical-align: top;"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('financial.index') }}" class="btn-gray" style="min-width: 100px; height: 36px; display: inline-flex; align-items: center; justify-content: center; vertical-align: top;">Reset</a>
            </div>
        </form>
    </div>

    <!-- Financial Table -->
    <div class="table-card">
        <div class="table-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; overflow:visible; position:relative; z-index:5;">
            @include('components.table-tools', [
                'tableId' => 'financialTable',
                'storageKey' => 'financial-columns',
                'columns' => [
                    'row_select' => 'Select',
                    'action' => 'Action',
                    'status' => 'Status',
                    'type' => 'Type',
                    'description' => 'Description',
                    'supplier' => 'Supplier',
                    'pr_amount' => 'PR Amount',
                    'pr_number' => 'PR #',
                    'po_amount' => 'PO Amount',
                    'po_number' => 'PO #',
                    'obr_number' => 'OBR #',
                    'voucher_number' => 'Voucher #',
                    'office_origin' => 'Office Origin',
                    'progress' => 'Progress',
                ],
                'lockedColumns' => ['row_select'],
                'reportTitle' => 'Financial Monitoring',
                'csvUrl' => request()->fullUrlWithQuery(['export' => 'csv']),
                'printUrl' => request()->fullUrlWithQuery(['export' => 'print']),
            ])
            <a href="{{ route('financial.create') }}" class="btn-red" style="min-width: 100px; height: 36px; display: inline-flex; align-items: center; justify-content: center;"><i class="fas fa-plus"></i> Add New Record</a>
        </div>

        <div style="overflow-x:auto; max-width:100%;">
            <table id="financialTable" style="min-width:1200px; width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:52px; border-bottom:2px solid #8b0000;">
                            <input type="checkbox" class="table-select-all" onclick="event.stopPropagation();">
                        </th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">ACTION</th>
                        <th style="text-align:center; padding:12px 50px; white-space:nowrap; width:180px; border-bottom:2px solid #8b0000; position: relative;">
                            <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleHeaderDropdown('statusDropdown', 'statusDropdownIcon', event)">
                                <span>STATUS</span>
                                <i class="fas fa-chevron-down" id="statusDropdownIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                            </div>
                            <div id="statusDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:140px; display:none; overflow:hidden;">
                                <a href="{{ request()->fullUrlWithQuery(['status' => 'ACTIVE']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Active</a>
                                <a href="{{ request()->fullUrlWithQuery(['status' => 'CANCELLED']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Cancelled</a>
                                <a href="{{ request()->fullUrlWithQuery(['status' => 'FINISHED']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Finished</a>
                                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="table-header-filter-link">All Status</a>
                            </div>
                        </th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:100px; border-bottom:2px solid #8b0000; position:relative;">
                            <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleHeaderDropdown('typeDropdown', 'typeDropdownIcon', event)">
                                <span>TYPE</span>
                                <i class="fas fa-chevron-down" id="typeDropdownIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                            </div>
                            <div id="typeDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:170px; display:none; overflow:hidden;">
                                <a href="{{ request()->fullUrlWithQuery(['type' => 'DV']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">DV</a>
                                <a href="{{ request()->fullUrlWithQuery(['type' => 'INSPEC']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">INSPEC</a>
                                <a href="{{ request()->fullUrlWithQuery(['type' => 'LIQUIDATION']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">LIQUIDATION</a>
                                <a href="{{ request()->fullUrlWithQuery(['type' => 'OBR']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">OBR</a>
                                <a href="{{ request()->fullUrlWithQuery(['type' => 'POST INSPECTION']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">POST INSPECTION</a>
                                <a href="{{ request()->fullUrlWithQuery(['type' => 'PAYROLL']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">PAYROLL</a>
                                <a href="{{ request()->fullUrlWithQuery(['type' => 'OPG']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">OPG</a>
                                <a href="{{ request()->fullUrlWithQuery(['type' => 'PR']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">PR</a>
                                <a href="{{ request()->fullUrlWithQuery(['type' => 'PR,PO']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">PR,PO</a>
                                <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="table-header-filter-link">All Type</a>
                            </div>
                        </th>
                        <th style="text-align:center; padding:12px 8px; min-width:200px; border-bottom:2px solid #8b0000; position:relative;">
                            <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleHeaderDropdown('descriptionSortDropdown', 'descriptionSortDropdownIcon', event)">
                                <span>DESCRIPTION</span>
                                <i class="fas fa-chevron-down" id="descriptionSortDropdownIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                            </div>
                            <div id="descriptionSortDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:180px; display:none; overflow:hidden;">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'newest']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Newest to Oldest</a>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'oldest']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Oldest to Newest</a>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'az']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">A-Z</a>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'za']) }}" class="table-header-filter-link">Z-A</a>
                            </div>
                        </th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">SUPPLIER</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000; position:relative;">
                            <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleHeaderDropdown('prAmountSortDropdown', 'prAmountSortDropdownIcon', event)">
                                <span>PR AMOUNT</span>
                                <i class="fas fa-chevron-down" id="prAmountSortDropdownIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                            </div>
                            <div id="prAmountSortDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:190px; display:none; overflow:hidden;">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'pr_highest']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Highest to Lowest</a>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'pr_lowest']) }}" class="table-header-filter-link">Lowest to Highest</a>
                            </div>
                        </th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:100px; border-bottom:2px solid #8b0000;">PR #</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000; position:relative;">
                            <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleHeaderDropdown('poAmountSortDropdown', 'poAmountSortDropdownIcon', event)">
                                <span>PO AMOUNT</span>
                                <i class="fas fa-chevron-down" id="poAmountSortDropdownIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                            </div>
                            <div id="poAmountSortDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:190px; display:none; overflow:hidden;">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'po_highest']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Highest to Lowest</a>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'po_lowest']) }}" class="table-header-filter-link">Lowest to Highest</a>
                            </div>
                        </th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:100px; border-bottom:2px solid #8b0000;">PO #</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:100px; border-bottom:2px solid #8b0000;">OBR #</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">VOUCHER #</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">OFFICE ORIGIN</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:100px; border-bottom:2px solid #8b0000;">PROGRESS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $index => $rec)
                    <tr class="clickable-row" data-href="{{ route('financial.show', $rec) }}" style="cursor: pointer;">
                        <td style="text-align:center; padding:20px 8px; white-space:nowrap; width:52px;" onclick="event.stopPropagation();">
                            <input type="checkbox" class="table-row-select">
                        </td>
                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;" onclick="event.stopPropagation();">
                            <div style="display:flex; gap:4px; align-items:center; justify-content:flex-start;">
                                <a href="{{ route('financial.edit', $rec) }}" class="btn-blue" title="Edit" style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($rec->pinnedByCurrentUser())
                                    <span title="Pinned" style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; border-radius:8px; background:#fff7ed; color:#c2410c; border:1px solid #fdba74;">
                                        <i class="fas fa-thumbtack"></i>
                                    </span>
                                @endif
                                <form action="{{ route('financial.destroy', $rec) }}" method="POST" style="display:inline;" id="deleteForm-{{ $rec->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-danger" title="Delete" onclick="confirmDelete({{ $rec->id }}, '{{ $rec->description ?? 'Financial Record' }}')" style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:180px;" onclick="event.stopPropagation();">
                            <select 
                                class="form-control inline-select 
                                {{ match($rec->status) {
                                    'ACTIVE' => 'status-active',
                                    'CANCELLED' => 'status-cancelled',
                                    'FINISHED' => 'status-finished',
                                    default => ''
                                } }}"
                                onchange="updateFinancialStatus(this, {{ $rec->id }}, this.value)">
                                
                                <option value="ACTIVE" {{ $rec->status=='ACTIVE'?'selected':'' }}>ACTIVE</option>
                                <option value="CANCELLED" {{ $rec->status=='CANCELLED'?'selected':'' }}>CANCELLED</option>
                                <option value="FINISHED" {{ $rec->status=='FINISHED'?'selected':'' }}>FINISHED</option>
                            </select>
                        </td>
                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:100px; font-weight: 600;">{{ $rec->type ?? '—' }}</td>
                        <td style="text-align:left; padding:20px 20px 20px 20px; min-width:200px; word-wrap:break-word; font-size: 13px; font-weight: 600;">{{ $rec->description ?? 'No Description' }}</td>
                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;">{{ $rec->supplier ?? '—' }}</td>
                        <td style="text-align:right; padding:20px 20px 20px 20px; white-space:nowrap; width:120px; font-weight:600;">{{ number_format($rec->pr_amount ?? 0, 2) }}</td>
                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:100px;">{{ $rec->pr_number ?? '—' }}</td>
                        <td style="text-align:right; padding:20px 20px 20px 20px; white-space:nowrap; width:120px; font-weight:600;">{{ number_format($rec->po_amount ?? 0, 2) }}</td>
                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:100px;">{{ $rec->po_number ?? '—' }}</td>
                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:100px;">{{ $rec->obr_number ?? '—' }}</td>
                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;">{{ $rec->voucher_number ?? '—' }}</td>
                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;">{{ $rec->originOffice?->code ?? '—' }}</td>
                        <td style="text-align:left; padding:20px 20px 20px 20px; width:100px; vertical-align: middle;">
                            @if($rec->progress)
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="flex: 1; min-width: 60px; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                                        <div style="height: 100%; background: linear-gradient(135deg, #c0392b 0%, #8b0000 100%); width: {{ $rec->progress }}%; transition: width 0.3s ease;"></div>
                                    </div>
                                    <span style="font-size: 12px; color: #64748b; font-weight: 500;">{{ $rec->progress }}</span>
                                </div>
                            @else
                                <span style="color: #9ca3af; font-size: 12px;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" style="text-align:center; padding:60px 20px;">
                            <div style="background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%); border:2px dashed rgba(192,57,43,0.2); border-radius:16px; padding:40px; max-width:720px; margin:0 auto;">
                                <i class="fas fa-file-invoice-dollar" style="font-size:48px; color:#c0392b; margin-bottom:16px;"></i>
                                <h3 style="color:#1a1a2e; margin-bottom:8px;">No Financial Records Found</h3>
                                <p style="color:#64748b; margin-bottom:20px;">No financial records match your current filters.</p>
                                <a href="{{ route('financial.create') }}" class="btn-red">
                                    <i class="fas fa-plus"></i> Add Your First Record
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="padding:16px 20px; display:flex; justify-content:center; align-items:center; gap:16px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                @if($records->onFirstPage())
                    <span style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#d1d5db; font-size:13px; font-weight:500; cursor:not-allowed;">
                        <i class="fas fa-chevron-left"></i> Previous
                    </span>
                @else
                    <a href="{{ $records->previousPageUrl() }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                @endif
                
                <div style="display:flex; gap:4px;">
                    @for($i = 1; $i <= min(3, $records->lastPage()); $i++)
                        @if($records->currentPage() == $i)
                            <span style="padding:8px 12px; background:linear-gradient(135deg, #c0392b 0%, #8b0000 100%); border:none; border-radius:6px; color:#ffffff; font-size:13px; font-weight:600; cursor:pointer;">{{ $i }}</span>
                        @else
                            <a href="{{ $records->url($i) }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">{{ $i }}</a>
                        @endif
                    @endfor
                </div>
                
                @if($records->hasMorePages())
                    <a href="{{ $records->nextPageUrl() }}" style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#64748b; font-size:13px; font-weight:500; text-decoration:none; cursor:pointer; transition:all 0.2s ease; display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span style="padding:8px 12px; background:#ffffff; border:1px solid #e5e7eb; border-radius:6px; color:#d1d5db; font-size:13px; font-weight:500; cursor:not-allowed;">
                        Next <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
        </div>
    </div>

    <!-- Notification Container -->
    <div class="notification-container" id="notificationContainer"></div>

    <style>
        /* Consistent Font System */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #2c3e50;
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

        .filter-box {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
        }

        .filter-box h3 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .form-group label {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-control {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
        }

        table th {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }

        table td {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            color: #2c3e50;
        }

        .badge {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
        }

        .badge-active { background: #27ae60; color: #fff; }
        .badge-cancelled { background: #e74c3c; color: #fff; }
        .badge-finished { background: #00b894; color: #fff; }
        .badge-warning { background: #f39c12; color: #fff; }
        .badge-info { background: #3498db; color: #fff; }
        .badge-primary { background: #9b59b6; color: #fff; }
        .badge-success { background: #27ae60; color: #fff; }
        .badge-completed { background: #16a085; color: #fff; }

        /* Inline Status Dropdown Styles */
        .inline-select {
            width: 148px;
            max-width: 100%;
            min-width: 148px;
            height: 36px;
            padding: 0 34px 0 12px !important;
            border-radius: 8px !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
            cursor: pointer;
            font-weight: 700;
            font-size: 12px !important;
            line-height: 1.2;
            letter-spacing: 0.01em;
            box-shadow: 0 3px 8px rgba(15, 23, 42, 0.05);
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: linear-gradient(45deg, transparent 50%, #64748b 50%), linear-gradient(135deg, #64748b 50%, transparent 50%);
            background-position: calc(100% - 16px) calc(50% - 3px), calc(100% - 11px) calc(50% - 3px);
            background-size: 5px 5px, 5px 5px;
            background-repeat: no-repeat;
            margin: 0 auto;
        }

        .inline-select.status-active {
            background: #bbf7d0 !important;
            color: #166534 !important;
            border-color: #86efac !important;
        }

        .inline-select.status-cancelled {
            background: #fecaca !important;
            color: #991b1b !important;
            border-color: #fca5a5 !important;
        }

        .inline-select.status-finished {
            background: #bfdbfe !important;
            color: #1d4ed8 !important;
            border-color: #93c5fd !important;
        }

        /* When dropdown is OPEN -> force neutral */
        .inline-select:focus {
            background: white !important;
            color: #333 !important;
            border-color: rgba(192, 57, 43, 0.42) !important;
            box-shadow: 0 0 0 3px rgba(192,57,43,0.1), 0 8px 18px rgba(15,23,42,0.08) !important;
        }

        /* FORCE DROPDOWN OPTIONS TO STAY CLEAN */
        .inline-select option {
            background: #ffffff !important;
            color: #000000 !important;
        }

        .btn-red, .btn-blue, .btn-green, .btn-gray, .btn-danger {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            font-weight: 600;
        }

        /* Modern Notification System */
        .notification-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            pointer-events: none;
        }

        .notification {
            background: #fff;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-left: 4px solid #e74c3c;
            min-width: 300px;
            max-width: 400px;
            pointer-events: all;
            animation: slideInRight 0.3s ease-out;
            position: relative;
            overflow: hidden;
        }

        .notification.success {
            border-left-color: #27ae60;
        }

        .notification.warning {
            border-left-color: #f39c12;
        }

        .notification.info {
            border-left-color: #3498db;
        }

        .notification-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .notification-title {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 600;
            font-size: 14px;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .notification-close {
            background: none;
            border: none;
            color: #7f8c8d;
            font-size: 18px;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .notification-close:hover {
            background: #f8f9fa;
            color: #2c3e50;
        }

        .notification-message {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #555;
            font-size: 14px;
            line-height: 1.4;
        }

        .notification-actions {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .notification-btn {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .notification-btn-confirm {
            background: #e74c3c;
            color: white;
        }

        .notification-btn-confirm:hover {
            background: #c0392b;
        }

        .notification-btn-cancel {
            background: #ecf0f1;
            color: #555;
        }

        .notification-btn-cancel:hover {
            background: #bdc3c7;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* Notification System Styles */
        .notification-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            pointer-events: none;
        }

        .notification {
            background: #fff;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-left: 4px solid #e74c3c;
            min-width: 300px;
            max-width: 400px;
            pointer-events: all;
            animation: slideInRight 0.3s ease-out;
            position: relative;
            overflow: hidden;
        }

        .notification.success {
            border-left-color: #27ae60;
        }

        .notification.warning {
            border-left-color: #f39c12;
        }

        .notification.info {
            border-left-color: #3498db;
        }

        .notification-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .notification-title {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 600;
            font-size: 14px;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .notification-close {
            background: none;
            border: none;
            color: #7f8c8d;
            font-size: 18px;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .notification-close:hover {
            background: #f8f9fa;
            color: #2c3e50;
        }

        .notification-message {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #555;
            font-size: 14px;
            line-height: 1.4;
        }

        .notification-actions {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .notification-btn {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .notification-btn-confirm {
            background: #e74c3c;
            color: white;
        }

        .notification-btn-confirm:hover {
            background: #c0392b;
        }

        .notification-btn-cancel {
            background: #ecf0f1;
            color: #555;
        }

        .notification-btn-cancel:hover {
            background: #bdc3c7;
        }

        .notification.removing {
            animation: slideOutRight 0.3s ease-out forwards;
        }
    </style>

    <script>
        // Modern Notification System - Local implementation
        function showNotification(options) {
            const {
                type = 'info',
                title = 'Notification',
                message = '',
                duration = 5000,
                actions = null,
                icon = null
            } = options;

            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;

            // Determine icon based on type
            let iconHtml = '';
            if (icon) {
                iconHtml = `<i class="${icon}"></i>`;
            } else {
                switch(type) {
                    case 'success':
                        iconHtml = '✓';
                        break;
                    case 'warning':
                        iconHtml = '⚠';
                        break;
                    case 'danger':
                        iconHtml = '✗';
                        break;
                    default:
                        iconHtml = 'ℹ';
                }
            }

            let actionsHtml = '';
            if (actions && actions.length > 0) {
                actionsHtml = '<div class="notification-actions">';
                actions.forEach(action => {
                    actionsHtml += `<button class="notification-btn ${action.class}" onclick="${action.onclick}">${action.text}</button>`;
                });
                actionsHtml += '</div>';
            }

            notification.innerHTML = `
                <div class="notification-header">
                    <div class="notification-title">${iconHtml} ${title}</div>
                    <button class="notification-close" onclick="removeNotification(this)">&times;</button>
                </div>
                <div class="notification-message">${message}</div>
                ${actionsHtml}
            `;

            container.appendChild(notification);

            // Auto-remove after duration
            if (duration > 0) {
                setTimeout(() => {
                    removeNotification(notification.querySelector('.notification-close'));
                }, duration);
            }

            return notification;
        }

        function removeNotification(element) {
            const notification = element.closest('.notification');
            if (notification) {
                notification.classList.add('removing');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }
        }

        // Confirmation dialog function
        function showConfirmDialog(options) {
            const {
                title = 'Confirm Action',
                message = 'Are you sure you want to proceed?',
                confirmText = 'Confirm',
                cancelText = 'Cancel',
                confirmClass = 'notification-btn-confirm',
                onConfirm = null,
                onCancel = null
            } = options;

            return new Promise((resolve) => {
                const notification = showNotification({
                    type: 'warning',
                    title: title,
                    message: message,
                    duration: 0, // Don't auto-close
                    actions: [
                        {
                            text: cancelText,
                            class: 'notification-btn-cancel',
                            onclick: `removeNotification(this.closest('.notification').querySelector('.notification-close')); confirmDialogCancel();`
                        },
                        {
                            text: confirmText,
                            class: confirmClass,
                            onclick: `removeNotification(this.closest('.notification').querySelector('.notification-close')); confirmDialogConfirm();`
                        }
                    ]
                });

                window.confirmDialogConfirm = () => {
                    if (onConfirm) onConfirm();
                    resolve(true);
                    delete window.confirmDialogConfirm;
                    delete window.confirmDialogCancel;
                };

                window.confirmDialogCancel = () => {
                    if (onCancel) onCancel();
                    resolve(false);
                    delete window.confirmDialogConfirm;
                    delete window.confirmDialogCancel;
                };
            });
        }

        function closeAllHeaderDropdowns() {
            document.querySelectorAll('[id$="Dropdown"]').forEach(dropdown => {
                if (dropdown.closest('th')) {
                    dropdown.style.display = 'none';
                }
            });
            document.querySelectorAll('[id$="DropdownIcon"]').forEach(icon => {
                icon.style.transform = 'rotate(0deg)';
            });
        }

        function toggleHeaderDropdown(dropdownId, iconId, event) {
            if (event) {
                event.stopPropagation();
            }

            const dropdown = document.getElementById(dropdownId);
            const icon = document.getElementById(iconId);
            const isOpen = dropdown.style.display === 'block';

            closeAllHeaderDropdowns();

            if (!isOpen) {
                dropdown.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
            }
        }

        function updateFinancialStatus(selectElement, recordId, newStatus) {
            const oldStatus = selectElement.getAttribute('data-old-status') || selectElement.value;
            
            // Show loading state
            selectElement.disabled = true;
            selectElement.style.opacity = '0.6';
            
            // API call to update status
            fetch(`/financial/${recordId}/update-status`, {
                method:'PATCH',
                headers:{
                    'X-CSRF-TOKEN':'{{ csrf_token() }}',
                    'Content-Type':'application/json'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Update the select element's classes to match new status
                    selectElement.className = selectElement.className.replace(/status-\w+/g, '');
                    selectElement.classList.add('form-control', 'inline-select', `status-${newStatus.toLowerCase()}`);
                    
                    // Show success notification
                    showNotification({
                        type: 'success',
                        title: 'Status Updated',
                        message: `Financial record status changed to ${newStatus}`,
                        duration: 3000
                    });
                    
                    // Update the row styling if needed
                    const row = selectElement.closest('tr');
                    if (newStatus === 'CANCELLED') {
                        row.style.opacity = '0.7';
                    } else {
                        row.style.opacity = '1';
                    }
                } else {
                    // Revert the change on error
                    selectElement.value = oldStatus;
                    showNotification({
                        type: 'warning',
                        title: 'Update Failed',
                        message: data.message || 'Failed to update status',
                        duration: 5000
                    });
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                // Revert the change on error
                selectElement.value = oldStatus;
                showNotification({
                    type: 'danger',
                    title: 'Error',
                    message: 'An error occurred while updating status',
                    duration: 5000
                });
            })
            .finally(() => {
                // Re-enable the select element
                selectElement.disabled = false;
                selectElement.style.opacity = '1';
                
                // Store current value as old status for next change
                selectElement.setAttribute('data-old-status', selectElement.value);
            });
        }

        function confirmDelete(recordId, description) {
            console.log('confirmDelete called with:', recordId, description); // Debug log
            
            showConfirmDialog({
                title: 'Delete Financial Record',
                message: `Are you sure you want to delete this financial record?<br><br><strong>Description:</strong> ${description}<br><strong>This action cannot be undone!</strong>`,
                confirmText: 'Delete',
                cancelText: 'Cancel',
                confirmClass: 'notification-btn-confirm',
                onConfirm: function() {
                    console.log('Delete confirmed, submitting form:', recordId); // Debug log
                    const form = document.getElementById(`deleteForm-${recordId}`);
                    if (form) {
                        form.submit();
                    } else {
                        console.error('Form not found:', `deleteForm-${recordId}`);
                    }
                }
            });
        }

        // Test function
        function testNotification() {
            showNotification({
                type: 'info',
                title: 'Test Notification',
                message: 'This is a test notification to verify the system is working.',
                duration: 3000
            });
        }

        // Auto-test on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Financial page loaded, notification system ready');
            
            // Uncomment to test automatically:
            // setTimeout(testNotification, 1000);

            document.addEventListener('click', function() {
                closeAllHeaderDropdowns();
            });
        });

        // Clickable rows functionality
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.clickable-row').forEach(row => {
                row.addEventListener('click', function(e) {
                    // Don't redirect if clicking on buttons or their children
                    if (e.target.closest('button') || e.target.closest('a')) {
                        return;
                    }
                    window.location.href = this.dataset.href;
                });
            });
        });

    </script>
</x-app-layout>
