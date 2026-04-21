<x-app-layout>
    <x-slot name="header">
        <h1>{{ !empty($isTravelOrderPage) ? 'Travel Orders' : 'Documents' }}</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / {{ !empty($isTravelOrderPage) ? 'Travel Orders' : 'Documents' }}</div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
    @endif

    @php
        $isOutgoingView = request('direction') === 'OUTGOING' || request('type') === 'TO';
        $isSpecificOutgoingScopeView = $isOutgoingView && filled(request('delivery_scope'));
        $isIncomingPage = request('direction') === 'INCOMING';
        $documentTypeOptions = ($isSpecificOutgoingScopeView || $isIncomingPage)
            ? ['MEMO', 'EO', 'SO', 'LETTER', 'SP', 'OTHERS']
            : ['MEMO', 'EO', 'SO', 'LETTER', 'SP', 'TO', 'OTHERS'];
        $typeLabel = match(request('type')) {
            'EO' => 'Executive Order',
            'SO' => 'Special Order',
            'SP' => 'SP',
            'TO' => 'Travel Order',
            default => request('type'),
        };
        $travelOrderTypeLabel = match(request('travel_order_type')) {
            'WITHIN_LA_UNION' => 'WITHIN LA UNION',
            'OUTSIDE_LA_UNION' => 'OUTSIDE LA UNION',
            'SPECIAL_ORDER' => 'SPECIAL ORDER',
            default => str_replace('_', ' ', (string) request('travel_order_type')),
        };
    @endphp

    @include('components.saved-filter-bar', [
        'module' => !empty($isTravelOrderPage) ? 'travel_orders' : 'documents',
        'savedFilters' => $savedFilters ?? collect(),
    ])

    <div class="filter-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="margin:0;">Search Filter</h3>
            @if(request()->hasAny(['direction', 'delivery_scope', 'status', 'type', 'travel_order_type', 'month', 'year', 'search', 'sort_by']))
                <div class="active-filter-list" style="justify-content:flex-end;margin-bottom:12px;">
                    <span class="active-filter-label">Active Filters:</span>
                    @if(request('direction'))
                        <span class="active-filter-pill">{{ request('direction') }} <a href="{{ request()->fullUrlWithQuery(['direction' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none;cursor:pointer;" title="Remove direction filter">&times;</a></span>
                    @endif
                    @if(request('delivery_scope'))
                        <span class="active-filter-pill">{{ request('delivery_scope') }} <a href="{{ request()->fullUrlWithQuery(['delivery_scope' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none;cursor:pointer;" title="Remove outgoing scope filter">&times;</a></span>
                    @endif
                    @if(request('status'))
                        <span class="active-filter-pill">{{ request('status') }} <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none;cursor:pointer;" title="Remove status filter">&times;</a></span>
                    @endif
                    @if(request('type'))
                        <span class="active-filter-pill">{{ $typeLabel }} <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none;cursor:pointer;" title="Remove type filter">&times;</a></span>
                    @endif
                    @if(request('travel_order_type'))
                        <span class="active-filter-pill">{{ $travelOrderTypeLabel }} <a href="{{ request()->fullUrlWithQuery(['travel_order_type' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none;cursor:pointer;" title="Remove travel order type filter">&times;</a></span>
                    @endif
                    @if(request('month'))
                        <span class="active-filter-pill">{{ request('month') }} <a href="{{ request()->fullUrlWithQuery(['month' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none;cursor:pointer;" title="Remove month filter">&times;</a></span>
                    @endif
                    @if(request('year'))
                        <span class="active-filter-pill">{{ request('year') }} <a href="{{ request()->fullUrlWithQuery(['year' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none;cursor:pointer;" title="Remove year filter">&times;</a></span>
                    @endif
                    @if(request('search'))
                        <span class="active-filter-pill">{{ request('search') }} <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none;cursor:pointer;" title="Remove search filter">&times;</a></span>
                    @endif
                    @if(request('sort_by'))
                        <span class="active-filter-pill">{{ match(request('sort_by')) {
                            'newest' => (!empty($isTravelOrderPage) ? 'PARTICULARS / PURPOSE: NEWEST TO OLDEST' : 'SUBJECT: NEWEST TO OLDEST'),
                            'oldest' => (!empty($isTravelOrderPage) ? 'PARTICULARS / PURPOSE: OLDEST TO NEWEST' : 'SUBJECT: OLDEST TO NEWEST'),
                            'az' => (!empty($isTravelOrderPage) ? 'PARTICULARS / PURPOSE: A-Z' : 'SUBJECT: A-Z'),
                            'za' => (!empty($isTravelOrderPage) ? 'PARTICULARS / PURPOSE: Z-A' : 'SUBJECT: Z-A'),
                            default => strtoupper(str_replace('_', ' ', request('sort_by')))
                        } }} <a href="{{ request()->fullUrlWithQuery(['sort_by' => null]) }}" class="badge bg-light text-dark" style="text-decoration:none;cursor:pointer;" title="Remove sort filter">&times;</a></span>
                    @endif
                </div>
            @endif
        </div>

        <form method="GET" action="{{ route('documents.index') }}">
            @foreach(['direction', 'status', 'delivery_scope', 'travel_order_type', 'sort_by'] as $field)
                @if(request($field))
                    <input type="hidden" name="{{ $field }}" value="{{ request($field) }}">
                @endif
            @endforeach
            @if(!empty($isTravelOrderPage))
                <input type="hidden" name="type" value="TO">
            @endif

            <div style="display:grid;grid-template-columns:1fr;gap:8px;">
                <div class="form-group" style="margin:0;">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Enter keywords...">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
                <div class="form-group" style="margin:0;margin-top:12px;">
                    <label>Month</label>
                    <select name="month" class="form-control">
                        <option value="">All Months</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group" style="margin:0;margin-top:12px;">
                    <label>Year</label>
                    <input type="number" name="year" class="form-control" value="{{ request('year', now()->year) }}" min="2020" max="2035">
                </div>
                @if(empty($isTravelOrderPage))
                    <div class="form-group" style="margin:0;margin-top:12px;">
                        <label>Document Type</label>
                        <select name="type" class="form-control" id="documentTypeFilter">
                            <option value="">All Types</option>
                            @foreach($documentTypeOptions as $t)
                                <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ $t === 'TO' ? 'TO - Travel Order' : $t }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <div class="form-group" style="display:flex;gap:12px;margin-top:24px;justify-content:flex-end;">
                <button type="submit" class="btn-red" style="min-width:100px;height:36px;display:inline-flex;align-items:center;justify-content:center;"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('documents.index', !empty($isTravelOrderPage) ? ['type' => 'TO'] : []) }}" class="btn-gray" style="min-width:100px;height:36px;display:inline-flex;align-items:center;justify-content:center;">Reset</a>
            </div>
        </form>
    </div>

    <div class="table-card">
        @php
            $documentColumns = !empty($isTravelOrderPage)
                ? [
                    'row_select' => 'Select',
                    'action' => 'Action',
                    'dts_number' => 'DTS Number',
                    'travel_order_type' => 'TO Type',
                    'travel_dates' => 'Date/s of Travel',
                    'travelers' => 'Name/s',
                    'destinations' => 'Destination/s',
                    'particulars' => 'Particulars / Purpose',
                    'status' => 'Status',
                ]
                : [
                    'row_select' => 'Select',
                    'action' => 'Action',
                    'tracking_code' => 'Tracking Code',
                    'picto_no' => 'PICTO No',
                    'number' => 'Number',
                    'subject' => 'Subject',
                    'originating_office' => 'Originating Office',
                    'outgoing_type' => 'Type Direction',
                    'status' => 'Status',
                    'date_received' => 'Date Received',
                ];

            $defaultHiddenColumns = [];
            if (!$isTravelOrderPage && $isOutgoingView) {
                $defaultHiddenColumns = ['tracking_code', 'number', 'originating_office'];
                if ($isSpecificOutgoingScopeView) {
                    $defaultHiddenColumns[] = 'outgoing_type';
                }
            }
        @endphp

        <div class="table-header" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;overflow:visible;position:relative;z-index:5;">
            @include('components.table-tools', [
                'tableId' => 'documentsTable',
                'storageKey' => !empty($isTravelOrderPage) ? 'documents-travel-order-columns' : ('documents-columns-' . (request('direction') ?: 'all') . '-' . (request('delivery_scope') ?: 'all') . '-' . (request('type') ?: 'all')),
                'columns' => $documentColumns,
                'defaultHidden' => $defaultHiddenColumns,
                'lockedColumns' => ['row_select'],
                'reportTitle' => !empty($isTravelOrderPage) ? 'Travel Orders' : 'Documents',
                'csvUrl' => request()->fullUrlWithQuery(['export' => 'csv']),
                'printUrl' => request()->fullUrlWithQuery(['export' => 'print']),
            ])

            <a href="{{ route('documents.create', !empty($isTravelOrderPage) ? ['document_type' => 'TO'] : []) }}" class="btn-red" style="min-width:100px;height:36px;display:inline-flex;align-items:center;justify-content:center;"><i class="fas fa-plus"></i> {{ !empty($isTravelOrderPage) ? 'Add Travel Order' : 'Add New Document' }}</a>
        </div>
        <div style="overflow-x:auto;max-width:100%;">
            <table id="documentsTable" style="min-width:{{ !empty($isTravelOrderPage) ? '1340px' : '1020px' }};width:100%;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:center;padding:12px 8px;white-space:nowrap;width:52px;border-bottom:2px solid #8b0000;">
                            <input type="checkbox" class="table-select-all" onclick="event.stopPropagation();">
                        </th>
                        <th style="text-align:center;padding:12px 8px;white-space:nowrap;width:120px;border-bottom:2px solid #8b0000;">ACTION</th>
                        @if(!empty($isTravelOrderPage))
                            <th style="text-align:center;padding:12px 8px;white-space:nowrap;width:160px;border-bottom:2px solid #8b0000;">DTS NUMBER</th>
                            <th style="text-align:center;padding:12px 8px;white-space:nowrap;width:170px;border-bottom:2px solid #8b0000; position:relative;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleDocumentHeaderDropdown('documentTravelTypeDropdown', 'documentTravelTypeIcon', event)">
                                    <span>TO TYPE</span>
                                    <i class="fas fa-chevron-down" id="documentTravelTypeIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                                </div>
                                <div id="documentTravelTypeDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:170px; display:none; overflow:hidden;">
                                    <a href="{{ request()->fullUrlWithQuery(['travel_order_type' => 'WITHIN_LA_UNION']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Within La Union</a>
                                    <a href="{{ request()->fullUrlWithQuery(['travel_order_type' => 'OUTSIDE_LA_UNION']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Outside La Union</a>
                                    <a href="{{ request()->fullUrlWithQuery(['travel_order_type' => 'SPECIAL_ORDER']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Special Order</a>
                                    <a href="{{ request()->fullUrlWithQuery(['travel_order_type' => null]) }}" class="table-header-filter-link">All TO Type</a>
                                </div>
                            </th>
                            <th style="text-align:center;padding:12px 8px;white-space:nowrap;width:160px;border-bottom:2px solid #8b0000;">DATE/S OF TRAVEL</th>
                            <th style="text-align:center;padding:12px 8px;min-width:220px;border-bottom:2px solid #8b0000;">NAME/S</th>
                            <th style="text-align:center;padding:12px 8px;min-width:220px;border-bottom:2px solid #8b0000;">DESTINATION/S</th>
                            <th style="text-align:center;padding:12px 8px;min-width:260px;border-bottom:2px solid #8b0000; position:relative;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleDocumentHeaderDropdown('documentPurposeSortDropdown', 'documentPurposeSortIcon', event)">
                                    <span>PARTICULARS / PURPOSE</span>
                                    <i class="fas fa-chevron-down" id="documentPurposeSortIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                                </div>
                                <div id="documentPurposeSortDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:180px; display:none; overflow:hidden;">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'newest']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Newest to Oldest</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'oldest']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Oldest to Newest</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'az']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">A-Z</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'za']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Z-A</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => null]) }}" class="table-header-filter-link">Default Order</a>
                                </div>
                            </th>
                            <th style="text-align:center;padding:12px 8px;white-space:nowrap;width:100px;border-bottom:2px solid #8b0000; position:relative;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleDocumentHeaderDropdown('documentStatusDropdown', 'documentStatusIcon', event)">
                                    <span>STATUS</span>
                                    <i class="fas fa-chevron-down" id="documentStatusIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                                </div>
                                <div id="documentStatusDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:150px; display:none; overflow:hidden;">
                                    <a href="{{ request()->fullUrlWithQuery(['status' => 'ONGOING']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Ongoing</a>
                                    <a href="{{ request()->fullUrlWithQuery(['status' => 'DELIVERED']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Delivered</a>
                                    <a href="{{ request()->fullUrlWithQuery(['status' => 'DONE']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Done</a>
                                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="table-header-filter-link">All Status</a>
                                </div>
                            </th>
                        @else
                            <th style="text-align:center;padding:12px 8px;white-space:nowrap;width:150px;border-bottom:2px solid #8b0000;">TRACKING CODE</th>
                            <th style="text-align:center;padding:12px 8px;white-space:nowrap;width:180px;border-bottom:2px solid #8b0000;">PICTO NO</th>
                            <th style="text-align:center;padding:12px 8px;white-space:nowrap;width:150px;border-bottom:2px solid #8b0000;">NUMBER</th>
                            <th style="text-align:center;padding:12px 8px;min-width:250px;border-bottom:2px solid #8b0000; position:relative;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleDocumentHeaderDropdown('documentSubjectSortDropdown', 'documentSubjectSortIcon', event)">
                                    <span>SUBJECT</span>
                                    <i class="fas fa-chevron-down" id="documentSubjectSortIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                                </div>
                                <div id="documentSubjectSortDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:180px; display:none; overflow:hidden;">
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'newest']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Newest to Oldest</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'oldest']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Oldest to Newest</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'az']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">A-Z</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'za']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Z-A</a>
                                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => null]) }}" class="table-header-filter-link">Default Order</a>
                                </div>
                            </th>
                            <th style="text-align:center;padding:12px 8px;white-space:nowrap;width:120px;border-bottom:2px solid #8b0000;">ORIGINATING OFFICE</th>
                            <th style="text-align:center;padding:12px 8px;white-space:nowrap;width:130px;border-bottom:2px solid #8b0000; position:relative;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleDocumentHeaderDropdown('documentDirectionDropdown', 'documentDirectionIcon', event)">
                                    <span>TYPE DIRECTION</span>
                                    <i class="fas fa-chevron-down" id="documentDirectionIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                                </div>
                                <div id="documentDirectionDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:160px; display:none; overflow:hidden;">
                                    <a href="{{ request()->fullUrlWithQuery(['direction' => 'INCOMING', 'delivery_scope' => null]) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Incoming</a>
                                    <a href="{{ request()->fullUrlWithQuery(['direction' => 'OUTGOING', 'delivery_scope' => null]) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Outgoing</a>
                                    <a href="{{ request()->fullUrlWithQuery(['direction' => null, 'delivery_scope' => null]) }}" class="table-header-filter-link">All Type Direction</a>
                                </div>
                            </th>
                            <th style="text-align:center;padding:12px 8px;white-space:nowrap;width:100px;border-bottom:2px solid #8b0000; position:relative;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer;" onclick="toggleDocumentHeaderDropdown('documentStatusDropdown', 'documentStatusIcon', event)">
                                    <span>STATUS</span>
                                    <i class="fas fa-chevron-down" id="documentStatusIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                                </div>
                                <div id="documentStatusDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:150px; display:none; overflow:hidden;">
                                    <a href="{{ request()->fullUrlWithQuery(['status' => 'ONGOING']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Ongoing</a>
                                    <a href="{{ request()->fullUrlWithQuery(['status' => 'DELIVERED']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Delivered</a>
                                    <a href="{{ request()->fullUrlWithQuery(['status' => 'DONE']) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Done</a>
                                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="table-header-filter-link">All Status</a>
                                </div>
                            </th>
                            <th style="text-align:center;padding:12px 8px;white-space:nowrap;width:120px;border-bottom:2px solid #8b0000;">DATE RECEIVED</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        <tr class="clickable-row" data-href="{{ route('documents.show', $doc) }}" style="cursor:pointer;">
                            <td style="text-align:center;padding:20px 8px;white-space:nowrap;width:52px;" onclick="event.stopPropagation();">
                                <input type="checkbox" class="table-row-select">
                            </td>
                            <td style="text-align:left;padding:20px;white-space:nowrap;width:120px;" onclick="event.stopPropagation();">
                                <div style="display:flex;gap:4px;align-items:center;justify-content:flex-start;">
                                    <a href="{{ route('documents.edit', $doc) }}" class="btn-blue" title="Edit" style="padding:6px 8px;min-width:32px;height:32px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-edit"></i></a>
                                    @if($doc->pinnedByCurrentUser())
                                        <span title="Pinned" style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; border-radius:8px; background:#fff7ed; color:#c2410c; border:1px solid #fdba74;">
                                            <i class="fas fa-thumbtack"></i>
                                        </span>
                                    @endif
                                    <form action="{{ route('documents.destroy', $doc) }}" method="POST" style="display:inline;" id="deleteForm-{{ $doc->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-danger" title="Delete" onclick='confirmDelete({{ $doc->id }}, @json($doc->subject ?? "Untitled Document"), @json($doc->dts_number ?? "No Tracking Code"))' style="padding:6px 8px;min-width:32px;height:32px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                            @if(!empty($isTravelOrderPage))
                                <td style="text-align:left;padding:20px;white-space:nowrap;width:160px;">{{ $doc->dts_number }}</td>
                                <td style="text-align:left;padding:20px;white-space:nowrap;width:170px;">{{ match($doc->travel_order_type) { 'WITHIN_LA_UNION' => 'Within La Union', 'OUTSIDE_LA_UNION' => 'Outside La Union', 'SPECIAL_ORDER' => 'Special Order', default => '—' } }}</td>
                                <td style="text-align:left;padding:20px;white-space:nowrap;width:160px;">{{ $doc->travel_dates ?? '—' }}</td>
                                <td style="text-align:left;padding:20px;min-width:220px;white-space:pre-line;">{{ $doc->travelers ?? '—' }}</td>
                                <td style="text-align:left;padding:20px;min-width:220px;">{{ $doc->destinations ?? '—' }}</td>
                                <td style="text-align:left;padding:20px;min-width:260px;">{{ $doc->particulars ?? $doc->subject ?? '—' }}</td>
                                <td style="text-align:left;padding:20px;white-space:nowrap;width:100px;">
                                    @php
                                        $badgeClass = match($doc->status) {
                                            'ONGOING' => 'badge-ongoing',
                                            'DELIVERED' => 'badge-delivered',
                                            'DONE' => 'badge-completed',
                                            default => ''
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $doc->status }}</span>
                                </td>
                            @else
                                <td style="text-align:left;padding:20px;white-space:nowrap;width:150px;">{{ $doc->dts_number }}</td>
                                <td style="text-align:left;padding:20px;white-space:nowrap;width:180px;">{{ $doc->doc_number ?? '—' }}</td>
                                <td style="text-align:left;padding:20px;white-space:nowrap;width:150px;">{{ $doc->memorandum_number ?? '—' }}</td>
                                <td style="text-align:left;padding:20px;min-width:250px;word-wrap:break-word;">{{ $doc->subject }}</td>
                                <td style="text-align:left;padding:20px;white-space:nowrap;width:120px;font-size:12px;">{{ $doc->originatingOffice->code ?? '—' }}</td>
                                <td style="text-align:left;padding:20px;white-space:nowrap;width:130px;">{{ match($doc->direction) { 'INCOMING' => 'Incoming', 'OUTGOING' => 'Outgoing', default => '—' } }}</td>
                                <td style="text-align:left;padding:20px;white-space:nowrap;width:100px;">
                                    @php
                                        $badgeClass = match($doc->status) {
                                            'ONGOING' => 'badge-ongoing',
                                            'DELIVERED' => 'badge-delivered',
                                            'DONE' => 'badge-completed',
                                            default => ''
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $doc->status }}</span>
                                </td>
                                <td style="text-align:left;padding:20px;white-space:nowrap;width:120px;">{{ $doc->date_received ? $doc->date_received->format('F d, Y') : ($doc->created_at ? $doc->created_at->format('F d, Y') : '—') }}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ !empty($isTravelOrderPage) ? 9 : 10 }}" style="text-align:center;padding:60px 20px;">
                                <div style="background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%);border:2px dashed rgba(192,57,43,0.2);border-radius:16px;padding:40px;max-width:720px;margin:0 auto;">
                                    <i class="fas fa-inbox" style="font-size:48px;color:#c0392b;margin-bottom:16px;"></i>
                                    <h3 style="color:#1a1a2e;margin-bottom:8px;">{{ !empty($isTravelOrderPage) ? 'No Travel Orders Found' : 'No Documents Found' }}</h3>
                                    <p style="color:#64748b;margin-bottom:20px;">{{ !empty($isTravelOrderPage) ? 'Start by adding your first travel order to the system.' : 'Start by adding your first document to the system.' }}</p>
                                    <a href="{{ route('documents.create', !empty($isTravelOrderPage) ? ['document_type' => 'TO'] : []) }}" class="btn-red"><i class="fas fa-plus"></i> {{ !empty($isTravelOrderPage) ? 'Add Travel Order' : 'Add New Document' }}</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding:16px 20px;display:flex;justify-content:center;align-items:center;gap:16px;">
            <div style="display:flex;align-items:center;gap:8px;">
                @if($documents->onFirstPage())
                    <span style="padding:8px 12px;background:#ffffff;border:1px solid #e5e7eb;border-radius:6px;color:#d1d5db;font-size:13px;font-weight:500;cursor:not-allowed;"><i class="fas fa-chevron-left"></i> Previous</span>
                @else
                    <a href="{{ $documents->previousPageUrl() }}" style="padding:8px 12px;background:#ffffff;border:1px solid #e5e7eb;border-radius:6px;color:#64748b;font-size:13px;font-weight:500;text-decoration:none;cursor:pointer;transition:all 0.2s ease;display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';"><i class="fas fa-chevron-left"></i> Previous</a>
                @endif

                <div style="display:flex;gap:4px;">
                    @for($i = 1; $i <= min(3, $documents->lastPage()); $i++)
                        @if($documents->currentPage() == $i)
                            <span style="padding:8px 12px;background:linear-gradient(135deg, #c0392b 0%, #8b0000 100%);border:none;border-radius:6px;color:#ffffff;font-size:13px;font-weight:600;cursor:pointer;">{{ $i }}</span>
                        @else
                            <a href="{{ $documents->url($i) }}" style="padding:8px 12px;background:#ffffff;border:1px solid #e5e7eb;border-radius:6px;color:#64748b;font-size:13px;font-weight:500;text-decoration:none;cursor:pointer;transition:all 0.2s ease;display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">{{ $i }}</a>
                        @endif
                    @endfor
                </div>

                @if($documents->hasMorePages())
                    <a href="{{ $documents->nextPageUrl() }}" style="padding:8px 12px;background:#ffffff;border:1px solid #e5e7eb;border-radius:6px;color:#64748b;font-size:13px;font-weight:500;text-decoration:none;cursor:pointer;transition:all 0.2s ease;display:inline-block;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#c0392b'; this.style.color='#c0392b';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e5e7eb'; this.style.color='#64748b';">Next <i class="fas fa-chevron-right"></i></a>
                @else
                    <span style="padding:8px 12px;background:#ffffff;border:1px solid #e5e7eb;border-radius:6px;color:#d1d5db;font-size:13px;font-weight:500;cursor:not-allowed;">Next <i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
    </div>

    <script>
        function closeDocumentHeaderDropdowns() {
            ['documentTravelTypeDropdown', 'documentPurposeSortDropdown', 'documentSubjectSortDropdown', 'documentDirectionDropdown', 'documentStatusDropdown'].forEach((id) => {
                const dropdown = document.getElementById(id);
                if (dropdown) {
                    dropdown.style.display = 'none';
                }
            });

            ['documentTravelTypeIcon', 'documentPurposeSortIcon', 'documentSubjectSortIcon', 'documentDirectionIcon', 'documentStatusIcon'].forEach((id) => {
                const icon = document.getElementById(id);
                if (icon) {
                    icon.style.transform = 'rotate(0deg)';
                }
            });
        }

        function toggleDocumentHeaderDropdown(dropdownId, iconId, event) {
            if (event) {
                event.stopPropagation();
            }

            const dropdown = document.getElementById(dropdownId);
            const icon = document.getElementById(iconId);
            const isOpen = dropdown && dropdown.style.display === 'block';

            closeDocumentHeaderDropdowns();

            if (dropdown && icon && !isOpen) {
                dropdown.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
            }
        }

        function confirmDelete(docId, subject, trackingCode) {
            if (window.confirm(`Delete this document?\n\nTitle: ${subject}\nTracking Code: ${trackingCode}`)) {
                const form = document.getElementById(`deleteForm-${docId}`);
                if (form) {
                    form.submit();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.clickable-row').forEach((row) => {
                row.addEventListener('click', function() {
                    const href = this.getAttribute('data-href');
                    if (href) {
                        window.location.href = href;
                    }
                });
            });

            const filterForm = document.querySelector('.filter-box form');
            const typeFilter = document.getElementById('documentTypeFilter');
            const travelTypeInput = filterForm ? filterForm.querySelector('input[name="travel_order_type"]') : null;
            const deliveryScopeInput = filterForm ? filterForm.querySelector('input[name="delivery_scope"]') : null;

            if (typeFilter && travelTypeInput) {
                typeFilter.addEventListener('change', function() {
                    if (this.value !== 'TO') {
                        travelTypeInput.value = '';
                    }
                });
            }

            if (deliveryScopeInput && !filterForm.querySelector('input[name="direction"]')) {
                deliveryScopeInput.value = '';
            }
        });

        document.addEventListener('click', function() {
            closeDocumentHeaderDropdowns();
        });
    </script>
</x-app-layout>
