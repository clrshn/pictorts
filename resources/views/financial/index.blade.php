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

    <!-- Search Filter -->
    <div class="filter-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="margin:0;">Search Filter</h3>
            @if(request()->hasAny(['status', 'type', 'search']))
                <div style="display:flex; gap:4px; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
                    <span style="color:#666; font-size:15px;">Active Filters:</span>
                    @if(request('status'))
                        <span class="badge" style="background:#1976d2; color:white; padding:1px 5px; border-radius:2px; display:flex; align-items:center; gap:3px; font-size:12px; white-space:nowrap;">
                            {{ request('status') }}
                            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove status filter">×</a>
                        </span>
                    @endif
                    @if(request('type'))
                        <span class="badge" style="background:#1976d2; color:white; padding:1px 5px; border-radius:2px; display:flex; align-items:center; gap:3px; font-size:12px; white-space:nowrap;">
                            {{ request('type') }}
                            <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove type filter">×</a>
                        </span>
                    @endif
                    @if(request('search'))
                        <span class="badge" style="background:#1976d2; color:white; padding:1px 5px; border-radius:2px; display:flex; align-items:center; gap:3px; font-size:12px; white-space:nowrap;">
                            {{ request('search') }}
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none; cursor:pointer;" title="Remove search filter">×</a>
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
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if(request('sort_by'))
                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            @endif
            <div style="display:grid; grid-template-columns: 1fr; gap:8px;">
                <div class="form-group" style="margin:0">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Enter keywords...">
                </div>
            </div>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:12px;">
                <div class="form-group" style="margin:0; margin-top:12px;">
                    <label>Type</label>
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        <option value="PR" {{ request('type') === 'PR' ? 'selected' : '' }}>PR</option>
                        <option value="PCV" {{ request('type') === 'PCV' ? 'selected' : '' }}>PCV</option>
                        <option value="DV" {{ request('type') === 'DV' ? 'selected' : '' }}>DV</option>
                        <option value="PO" {{ request('type') === 'PO' ? 'selected' : '' }}>PO</option>
                        <option value="INSPEC" {{ request('type') === 'INSPEC' ? 'selected' : '' }}>INSPEC</option>
                        <option value="OBR" {{ request('type') === 'OBR' ? 'selected' : '' }}>OBR</option>
                        <option value="IAR" {{ request('type') === 'IAR' ? 'selected' : '' }}>IAR</option>
                        <option value="IT" {{ request('type') === 'IT' ? 'selected' : '' }}>IT</option>
                        <option value="AIR" {{ request('type') === 'AIR' ? 'selected' : '' }}>AIR</option>
                        <option value="PRI" {{ request('type') === 'PRI' ? 'selected' : '' }}>PRI</option>
                        <option value="POST INSPECTION" {{ request('type') === 'POST INSPECTION' ? 'selected' : '' }}>POST INSPECTION</option>
                        <option value="LIQUADATION" {{ request('type') === 'LIQUADATION' ? 'selected' : '' }}>LIQUADATION</option>
                        <option value="PAYROLL" {{ request('type') === 'PAYROLL' ? 'selected' : '' }}>PAYROLL</option>
                        <option value="ACCTG" {{ request('type') === 'ACCTG' ? 'selected' : '' }}>ACCTG</option>
                        <option value="PTO" {{ request('type') === 'PTO' ? 'selected' : '' }}>PTO</option>
                        <option value="OPG" {{ request('type') === 'OPG' ? 'selected' : '' }}>OPG</option>
                    </select>
                </div>
                <div class="form-group" style="margin:0; margin-top:12px;">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
                        <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                        <option value="FINISHED" {{ request('status') === 'FINISHED' ? 'selected' : '' }}>Finished</option>
                    </select>
                </div>
                <div class="form-group" style="margin:0; margin-top:12px;">
                    <label>Sort By</label>
                    <select name="sort_by" class="form-control">
                        <option value="">Default</option>
                        <option value="newest" {{ request('sort_by')=='newest'?'selected':'' }}>Newest to Oldest</option>
                        <option value="oldest" {{ request('sort_by')=='oldest'?'selected':'' }}>Oldest to Newest</option>
                        <option value="az" {{ request('sort_by')=='az'?'selected':'' }}>A-Z (Description)</option>
                        <option value="za" {{ request('sort_by')=='za'?'selected':'' }}>Z-A (Description)</option>
                        <option value="highest" {{ request('sort_by')=='highest'?'selected':'' }}>Highest to Lowest (PR Amount)</option>
                        <option value="lowest" {{ request('sort_by')=='lowest'?'selected':'' }}>Lowest to Highest (PR Amount)</option>
                    </select>
                </div>
                <div class="form-group" style="margin:0; margin-top:12px; display:flex; align-items:flex-end; gap:8px;">
                    <button type="submit" class="btn-red"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('financial.index') }}" class="btn-gray">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Financial Table -->
    <div class="table-card">
        <div class="table-header" style="display: flex; justify-content: flex-end; align-items: center;">
            <a href="{{ route('financial.create') }}" class="btn-red" style="display: flex; align-items: center; justify-content: center; text-align: center;"><i class="fas fa-plus"></i> Add New Record</a>
        </div>

        <div style="overflow-x:auto; max-width:100%;">
            <table style="min-width:1200px; width:100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">ACTION</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:100px; border-bottom:2px solid #8b0000;">STATUS</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:100px; border-bottom:2px solid #8b0000;">TYPE</th>
                        <th style="text-align:center; padding:12px 8px; min-width:200px; border-bottom:2px solid #8b0000;">DESCRIPTION</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">SUPPLIER</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">PR AMOUNT</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:100px; border-bottom:2px solid #8b0000;">PR #</th>
                        <th style="text-align:center; padding:12px 8px; white-space:nowrap; width:120px; border-bottom:2px solid #8b0000;">PO AMOUNT</th>
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
                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:120px;" onclick="event.stopPropagation();">
                            <div style="display:flex; gap:4px; align-items:center; justify-content:flex-start;">
                                <a href="{{ route('financial.edit', $rec) }}" class="btn-blue" title="Edit" style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('financial.destroy', $rec) }}" method="POST" style="display:inline;" id="deleteForm-{{ $rec->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-danger" title="Delete" onclick="confirmDelete({{ $rec->id }}, '{{ $rec->description ?? 'Financial Record' }}')" style="padding:6px 8px; min-width:32px; height:32px; display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td style="text-align:left; padding:20px 20px 20px 20px; white-space:nowrap; width:100px;">
                            <span class="badge badge-{{ $rec->status == 'ACTIVE' ? 'ongoing' : ($rec->status == 'CANCELLED' ? 'cancelled' : 'completed') }}" style="font-size: 11px; padding: 4px 8px;">{{ $rec->status }}</span>
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
                        <td colspan="11" style="text-align:center; padding:60px;">
                            <div style="background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%); border:2px dashed rgba(192,57,43,0.2); border-radius:16px; padding:40px;">
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

        function toggleSort(column) {
            const urlParams = new URLSearchParams(window.location.search);
            
            // Remove all column sort parameters
            urlParams.delete('description_sort');
            urlParams.delete('pr_amount_sort');
            urlParams.delete('pr_number_sort');
            
            // Get current sort state for this column
            const currentSort = urlParams.get(column + '_sort') || '';
            
            // Toggle between desc, asc, and none
            let newSort = 'desc';
            if (currentSort === 'desc') {
                newSort = 'asc';
            } else if (currentSort === 'asc') {
                newSort = ''; // Remove sort to go back to default
            }
            
            // Set new sort for this column
            if (newSort) {
                urlParams.set(column + '_sort', newSort);
            } else {
                urlParams.delete(column + '_sort');
            }
            
            // Reload page with new sort
            window.location.href = window.location.pathname + '?' + urlParams.toString();
        }

        // Highlight active sort arrows
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            
            ['description', 'pr_amount', 'pr_number'].forEach(column => {
                const sortValue = urlParams.get(column + '_sort');
                const icon = document.getElementById(column + '-sort');
                
                if (icon) {
                    if (sortValue === 'asc') {
                        icon.className = 'fas fa-chevron-up';
                        icon.style.color = '#8b0000';
                    } else if (sortValue === 'desc') {
                        icon.className = 'fas fa-chevron-down';
                        icon.style.color = '#8b0000';
                    } else {
                        icon.className = 'fas fa-chevron-down';
                        icon.style.color = '#ccc';
                    }
                }
            });
        });
    </script>
</x-app-layout>
