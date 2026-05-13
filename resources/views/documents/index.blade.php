<x-app-layout>
    <x-slot name="header">
        <h1>{{ !empty($isTravelOrderPage) ? 'Travel Orders' : 'Documents' }}</h1>
        <div class="breadcrumb"><a href="{{ route('dashboard') }}">Home</a> / {{ !empty($isTravelOrderPage) ? 'Travel Orders' : 'Documents' }}</div>
    </x-slot>



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
                    'outgoing_type' => 'Communication Type',
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

            <button type="button" class="btn-red" style="min-width:100px;height:36px;display:inline-flex;align-items:center;justify-content:center;" onclick="openDocumentFormModal('documentCreateModal')"><i class="fas fa-plus"></i> {{ !empty($isTravelOrderPage) ? 'Add Travel Order' : 'Add New Document' }}</button>
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
                                    <span>COMMUNICATION TYPE</span>
                                    <i class="fas fa-chevron-down" id="documentDirectionIcon" style="font-size:10px; transition:transform 0.3s ease;"></i>
                                </div>
                                <div id="documentDirectionDropdown" style="position:absolute; top:100%; left:50%; transform:translateX(-50%); background:white; border:1px solid #ddd; border-radius:8px; box-shadow:0 10px 24px rgba(15,23,42,0.14); z-index:1000; min-width:160px; display:none; overflow:hidden;">
                                    <a href="{{ request()->fullUrlWithQuery(['direction' => 'INCOMING', 'delivery_scope' => null]) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Incoming</a>
                                    <a href="{{ request()->fullUrlWithQuery(['direction' => 'OUTGOING', 'delivery_scope' => null]) }}" class="table-header-filter-link" style="border-bottom:1px solid #eee;">Outgoing</a>
                                    <a href="{{ request()->fullUrlWithQuery(['direction' => null, 'delivery_scope' => null]) }}" class="table-header-filter-link">All Communication Types</a>
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
                                    <button
                                        type="button"
                                        class="btn-blue"
                                        title="Edit"
                                        style="padding:6px 8px;min-width:32px;height:32px;display:flex;align-items:center;justify-content:center;"
                                        data-document-id="{{ $doc->id }}"
                                        data-document-dts-number="{{ e($doc->dts_number) }}"
                                        data-document-type="{{ $doc->document_type }}"
                                        data-direction="{{ $doc->direction }}"
                                        data-delivery-scope="{{ $doc->delivery_scope }}"
                                        data-originating-office="{{ $doc->originating_office }}"
                                        data-to-office="{{ $doc->to_office }}"
                                        data-memorandum-number="{{ e($doc->memorandum_number ?? '') }}"
                                        data-travel-order-type="{{ $doc->travel_order_type }}"
                                        data-travel-dates="{{ e($doc->travel_dates ?? '') }}"
                                        data-destinations="{{ e($doc->destinations ?? '') }}"
                                        data-travelers="{{ e($doc->travelers ?? '') }}"
                                        data-subject="{{ e($doc->subject ?? '') }}"
                                        data-opg-reference-no="{{ e($doc->opg_reference_no ?? '') }}"
                                        data-opa-reference-no="{{ e($doc->opa_reference_no ?? '') }}"
                                        data-governors-instruction="{{ e($doc->governors_instruction ?? '') }}"
                                        data-administrators-instruction="{{ e($doc->administrators_instruction ?? '') }}"
                                        data-returned="{{ e($doc->returned ?? '') }}"
                                        data-opg-action-slip="{{ e($doc->opg_action_slip ?? '') }}"
                                        data-dts-no="{{ e($doc->dts_no ?? '') }}"
                                        data-particulars="{{ e($doc->particulars ?? '') }}"
                                        data-period="{{ e($doc->period ?? '') }}"
                                        data-action-required="{{ e($doc->action_required ?? '') }}"
                                        data-endorsed-to="{{ e($doc->endorsed_to ?? '') }}"
                                        data-date-received="{{ $doc->date_received ? $doc->date_received->format('Y-m-d') : '' }}"
                                        data-received-via-online="{{ $doc->received_via_online ? '1' : '0' }}"
                                        data-status="{{ $doc->status }}"
                                        data-remarks="{{ e($doc->remarks ?? '') }}"
                                        data-shared-drive-link="{{ e($doc->shared_drive_link ?? '') }}"
                                        onclick="event.stopPropagation(); openDocumentEditModal(this)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($doc->pinnedByCurrentUser())
                                        <span title="Pinned" style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; border-radius:8px; background:#fff7ed; color:#c2410c; border:1px solid #fdba74;">
                                            <i class="fas fa-thumbtack"></i>
                                        </span>
                                    @endif
                                    @if(auth()->user()?->isAdmin())
                                        <form action="{{ route('documents.destroy', $doc) }}" method="POST" style="display:inline;" id="deleteForm-{{ $doc->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-danger" title="Delete" onclick='event.stopPropagation(); openDocumentDeleteModal({{ $doc->id }}, @json($doc->subject ?? "Untitled Document"), @json($doc->dts_number ?? "No Tracking Code"))' style="padding:6px 8px;min-width:32px;height:32px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
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
                                    <button type="button" class="btn-red" onclick="openDocumentFormModal('documentCreateModal')"><i class="fas fa-plus"></i> {{ !empty($isTravelOrderPage) ? 'Add Travel Order' : 'Add New Document' }}</button>
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

    <div id="documentCreateModal" class="financial-form-modal" style="display:none;">
        <div class="financial-form-modal__dialog" style="max-width:1180px;">
            <div class="financial-form-modal__header">
                <div>
                    <div class="financial-form-modal__title">{{ !empty($isTravelOrderPage) ? 'Add Travel Order' : 'Add New Document' }}</div>
                    <div class="financial-form-modal__subtitle">Create or edit records without leaving the current list.</div>
                </div>
                <button type="button" onclick="closeDocumentFormModal('documentCreateModal')" title="Close" aria-label="Close" style="border:none; background:transparent; color:#64748b; font-size:18px; font-weight:700; line-height:1; padding:0; width:24px; height:24px; display:flex; align-items:center; justify-content:center; cursor:pointer;">&times;</button>
            </div>
            @include('documents._form', [
                'offices' => $offices,
                'formMode' => 'create',
                'isModal' => true,
                'isTravelOrder' => !empty($isTravelOrderPage),
                'returnUrl' => request()->fullUrl(),
                'formKey' => !empty($isTravelOrderPage) ? 'document-create-travel-modal' : 'document-create-modal',
            ])
        </div>
    </div>

    <div id="documentEditModal" class="financial-form-modal" style="display:none;">
        <div class="financial-form-modal__dialog" style="max-width:1180px;">
            <div class="financial-form-modal__header">
                <div>
                    <div class="financial-form-modal__title">Edit Document</div>
                    <div class="financial-form-modal__subtitle" id="documentEditModalSubtitle">Create or edit records without leaving the current list.</div>
                </div>
                <button type="button" onclick="closeDocumentFormModal('documentEditModal')" title="Close" aria-label="Close" style="border:none; background:transparent; color:#64748b; font-size:18px; font-weight:700; line-height:1; padding:0; width:24px; height:24px; display:flex; align-items:center; justify-content:center; cursor:pointer;">&times;</button>
            </div>
            @include('documents._form', [
                'document' => new \App\Models\Document(),
                'offices' => $offices,
                'formMode' => 'edit',
                'isModal' => true,
                'isTravelOrder' => false,
                'formAction' => '#',
                'returnUrl' => request()->fullUrl(),
                'formKey' => 'document-edit-modal-shared',
            ])
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

        function openDocumentDeleteModal(docId, subject, trackingCode) {
            showConfirmDialog({
                title: 'Delete Document',
                message: `Are you sure you want to delete this document?<br><br><strong>Title:</strong> ${subject || 'Untitled Document'}<br><strong>Tracking Code:</strong> ${trackingCode || 'No Tracking Code'}<br><strong>This action cannot be undone!</strong>`,
                confirmText: 'Delete',
                cancelText: 'Cancel',
                confirmClass: 'notification-btn-confirm',
                onConfirm: function() {
                    const form = document.getElementById(`deleteForm-${docId}`);
                    if (form) {
                        form.submit();
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.clickable-row').forEach((row) => {
                row.addEventListener('click', function(event) {
                    if (event.target.closest('button') || event.target.closest('a') || event.target.closest('form') || event.target.closest('input') || event.target.closest('select') || event.target.closest('textarea') || event.target.closest('label')) {
                        return;
                    }

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

        function openDocumentFormModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) {
                return;
            }

            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function openDocumentEditModal(button) {
            const modal = document.getElementById('documentEditModal');
            if (!modal || !button) {
                return;
            }

            const docId = button.dataset.documentId;
            const form = modal.querySelector('form');
            if (!docId || !form) {
                return;
            }

            form.action = `/documents/${docId}`;

            const subtitle = document.getElementById('documentEditModalSubtitle');
            if (subtitle) {
                subtitle.textContent = button.dataset.documentDtsNumber || 'Create or edit records without leaving the current list.';
            }

            const setValue = (name, value) => {
                const field = form.querySelector(`[name="${name}"]`);
                if (!field) return;
                field.value = value ?? '';
                field.dispatchEvent(new Event('change', { bubbles: true }));
            };

            setValue('document_type', button.dataset.documentType);
            setValue('direction', button.dataset.direction);
            setValue('delivery_scope', button.dataset.deliveryScope);
            setValue('originating_office', button.dataset.originatingOffice);
            setValue('to_office', button.dataset.toOffice);
            setValue('memorandum_number', button.dataset.memorandumNumber);
            setValue('travel_order_type', button.dataset.travelOrderType);
            setValue('travel_dates', button.dataset.travelDates);
            setValue('destinations', button.dataset.destinations);
            setValue('travelers', button.dataset.travelers);
            setValue('subject', button.dataset.subject);
            setValue('opg_reference_no', button.dataset.opgReferenceNo);
            setValue('opa_reference_no', button.dataset.opaReferenceNo);
            setValue('governors_instruction', button.dataset.governorsInstruction);
            setValue('administrators_instruction', button.dataset.administratorsInstruction);
            setValue('returned', button.dataset.returned);
            setValue('opg_action_slip', button.dataset.opgActionSlip);
            setValue('dts_no', button.dataset.dtsNo);
            setValue('particulars', button.dataset.particulars);
            setValue('period', button.dataset.period);
            setValue('action_required', button.dataset.actionRequired);
            setValue('endorsed_to', button.dataset.endorsedTo);
            setValue('date_received', button.dataset.dateReceived);
            setValue('received_via_online', button.dataset.receivedViaOnline || '0');
            setValue('status', button.dataset.status || 'ONGOING');
            setValue('remarks', button.dataset.remarks);
            setValue('shared_drive_link', button.dataset.sharedDriveLink);
            setValue('modal_record_id', docId);

            openDocumentFormModal('documentEditModal');
        }

        function closeDocumentFormModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) {
                return;
            }

            modal.style.display = 'none';
            document.body.style.overflow = '';
        }

        document.addEventListener('click', function(event) {
            document.querySelectorAll('.financial-form-modal').forEach((modal) => {
                if (event.target === modal) {
                    closeDocumentFormModal(modal.id);
                }
            });
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.financial-form-modal').forEach((modal) => {
                    if (modal.style.display === 'block') {
                        closeDocumentFormModal(modal.id);
                    }
                });
            }
        });

        @if($errors->any() || session('duplicate_warning'))
            window.addEventListener('load', function() {
                @if(old('modal_mode') === 'edit' && old('modal_record_id'))
                    const editButton = document.querySelector('[data-document-id="{{ old('modal_record_id') }}"]');
                    if (editButton) {
                        openDocumentEditModal(editButton);
                    }
                @else
                    openDocumentFormModal('documentCreateModal');
                @endif
            });
        @endif
    </script>
</x-app-layout>
