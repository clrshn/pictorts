<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\Office;
use App\Models\SavedFilter;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\InAppNotificationService;
use App\Support\TableExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    private const TRAVEL_ORDER_TYPES = [
        'WITHIN_LA_UNION',
        'OUTSIDE_LA_UNION',
        'SPECIAL_ORDER',
    ];

    private function isTravelOrderRequest(Request $request): bool
    {
        return $request->type === 'TO';
    }

    private function ensureDocumentActionAllowed(Document $document, bool $expectsJson = false, string $action = 'update')
    {
        // Approved or pending-approval records are intentionally guarded to preserve
        // workflow integrity and prevent regular users from changing reviewed data.
        $approval = $document->approval;

        if (!auth()->user()?->isAdmin() && $approval?->status === 'pending') {
            return $expectsJson
                ? response()->json(['success' => false, 'message' => 'This document has a pending approval request and is temporarily locked.'], 422)
                : redirect()->back()->with('warning', 'This document has a pending approval request and is temporarily locked.');
        }

        if (!auth()->user()?->isAdmin() && in_array($action, ['update', 'delete'], true) && $approval?->status === 'approved') {
            return $expectsJson
                ? response()->json(['success' => false, 'message' => 'This document is already approved. Only an admin can modify it now.'], 422)
                : redirect()->back()->with('warning', 'This document is already approved. Only an admin can modify it now.');
        }

        return null;
    }

    private function findPotentialDuplicates(Request $request, ?Document $ignore = null)
    {
        // Duplicate detection is warning-based instead of hard-unique because legacy
        // office records may contain near-matches that still need manual review.
        $subject = trim((string) ($request->input('subject') ?: $request->input('particulars')));
        $hasReferenceFields = $subject !== ''
            || $request->filled('memorandum_number')
            || $request->filled('opg_reference_no')
            || $request->filled('opa_reference_no')
            || $request->filled('dts_no')
            || $request->filled('shared_drive_link');

        if (!$hasReferenceFields) {
            return collect();
        }

        return Document::query()
            ->with('originatingOffice')
            ->when($ignore, fn ($q) => $q->whereKeyNot($ignore->id))
            ->when($request->filled('originating_office'), fn ($q) => $q->where('originating_office', $request->originating_office))
            ->where(function ($q) use ($request, $subject) {
                if ($subject !== '') {
                    $q->orWhereRaw('LOWER(subject) = ?', [strtolower($subject)])
                        ->orWhereRaw('LOWER(COALESCE(particulars, "")) = ?', [strtolower($subject)]);
                }

                if ($request->filled('memorandum_number')) {
                    $q->orWhere('memorandum_number', $request->memorandum_number);
                }

                if ($request->filled('opg_reference_no')) {
                    $q->orWhere('opg_reference_no', $request->opg_reference_no);
                }

                if ($request->filled('opa_reference_no')) {
                    $q->orWhere('opa_reference_no', $request->opa_reference_no);
                }

                if ($request->filled('dts_no')) {
                    $q->orWhere('dts_no', $request->dts_no);
                }

                if ($request->filled('shared_drive_link')) {
                    $q->orWhere('shared_drive_link', $request->shared_drive_link);
                }
            })
            ->latest()
            ->limit(5)
            ->get();
    }

    public function index(Request $request)
    {
        // This index method doubles as both the standard table view and the source
        // for CSV / print / PDF reports, so all filter logic is centralized here.
        $query = Document::with(['originatingOffice', 'destinationOffice', 'currentOffice', 'holder']);
        $isTravelOrderPage = $this->isTravelOrderRequest($request);
        $sortBy = $request->get('sort_by');
        $exportMode = $request->get('export');

        // Filter by document type tab
        if ($request->filled('type') && $request->type !== 'ALL') {
            $query->where('document_type', $request->type);
        }

        if ($isTravelOrderPage) {
            $query->where('direction', 'OUTGOING')
                ->where('delivery_scope', 'INTERNAL');
        }

        if ($request->filled('travel_order_type')) {
            $query->where('travel_order_type', $request->travel_order_type);
        }

        // Filter by direction
        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        if ($request->filled('delivery_scope')) {
            $query->where('delivery_scope', $request->delivery_scope);
        }

        if ($request->boolean('pinned_only')) {
            $query->whereHas('pins', fn ($pinQuery) => $pinQuery->where('user_id', auth()->id()));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by month
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search across all fields
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('dts_number', 'like', "%{$search}%")
                  ->orWhere('picto_number', 'like', "%{$search}%")
                  ->orWhere('doc_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('travel_dates', 'like', "%{$search}%")
                  ->orWhere('travelers', 'like', "%{$search}%")
                  ->orWhere('destinations', 'like', "%{$search}%")
                  ->orWhere('endorsed_to', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhereHas('originatingOffice', function ($oq) use ($search) {
                      $oq->where('code', 'like', "%{$search}%")
                          ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sort by date
        if ($sortBy === 'newest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sortBy === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sortBy === 'az') {
            if ($isTravelOrderPage) {
                $query->orderByRaw('COALESCE(particulars, subject, \'\') asc');
            } else {
                $query->orderBy('subject', 'asc');
            }
        } elseif ($sortBy === 'za') {
            if ($isTravelOrderPage) {
                $query->orderByRaw('COALESCE(particulars, subject, \'\') desc');
            } else {
                $query->orderBy('subject', 'desc');
            }
        } else {
            $query->orderBy('doc_number', 'asc');
        }

        if ($exportMode === 'csv') {
            if ($isTravelOrderPage) {
                $rows = $query->get()->map(function ($doc) {
                    return [
                        $doc->dts_number,
                        match ($doc->travel_order_type) {
                            'WITHIN_LA_UNION' => 'Within La Union',
                            'OUTSIDE_LA_UNION' => 'Outside La Union',
                            'SPECIAL_ORDER' => 'Special Order',
                            default => '—',
                        },
                        $doc->travel_dates ?? '—',
                        preg_replace("/\r\n|\r|\n/", ' | ', $doc->travelers ?? '—'),
                        $doc->destinations ?? '—',
                        $doc->particulars ?? $doc->subject ?? '—',
                        $doc->status,
                        $doc->remarks ?? '—',
                    ];
                })->all();

                return TableExport::csv('travel-orders-report.csv', ['DTS Number', 'Travel Order Type', 'Date/s of Travel', 'Name/s', 'Destination/s', 'Particulars / Purpose', 'Status', 'Remarks'], $rows);
            }

            $rows = $query->get()->map(function ($doc) {
                return [
                    $doc->dts_number,
                    $doc->doc_number ?? '—',
                    $doc->memorandum_number ?? '—',
                    $doc->subject ?? '—',
                    $doc->originatingOffice->code ?? '—',
                    match ($doc->direction) {
                        'INCOMING' => 'Incoming',
                        'OUTGOING' => 'Outgoing',
                        default => '—',
                    },
                    $doc->status,
                    $doc->date_received ? $doc->date_received->format('F d, Y') : ($doc->created_at ? $doc->created_at->format('F d, Y') : '—'),
                    $doc->remarks ?? '—',
                ];
            })->all();

            return TableExport::csv('documents-report.csv', ['Tracking Code', 'PICTO No', 'Number', 'Subject', 'Originating Office', 'Type Direction', 'Status', 'Date Received', 'Remarks'], $rows);
        }

        if (in_array($exportMode, ['print', 'pdf'], true)) {
            if ($isTravelOrderPage) {
                $availableColumns = [
                    'dts_number' => 'DTS Number',
                    'travel_order_type' => 'Travel Order Type',
                    'travel_dates' => 'Date/s of Travel',
                    'travelers' => 'Name/s',
                    'destinations' => 'Destination/s',
                    'particulars' => 'Particulars / Purpose',
                    'status' => 'Status',
                ];

                $rows = $query->get()->map(function ($doc) {
                    return [
                        'dts_number' => $doc->dts_number,
                        'travel_order_type' => match ($doc->travel_order_type) {
                            'WITHIN_LA_UNION' => 'Within La Union',
                            'OUTSIDE_LA_UNION' => 'Outside La Union',
                            'SPECIAL_ORDER' => 'Special Order',
                            default => '—',
                        },
                        'travel_dates' => $doc->travel_dates ?? '—',
                        'travelers' => preg_replace("/\r\n|\r|\n/", ', ', $doc->travelers ?? '—'),
                        'destinations' => $doc->destinations ?? '—',
                        'particulars' => $doc->particulars ?? $doc->subject ?? '—',
                        'status' => $doc->status,
                    ];
                })->all();

                $visibleKeys = TableExport::normalizeVisibleColumns($request->get('visible_columns'), $availableColumns);
                [$headers, $printRows] = TableExport::projectRows($availableColumns, $rows, $visibleKeys);

                $responseMethod = $exportMode === 'pdf' ? 'pdfTable' : 'printTable';

                return TableExport::{$responseMethod}('Travel Orders', $headers, $printRows, [
                    'Search' => $request->search ?: 'All records',
                    'Travel Order Type' => $request->travel_order_type ? str_replace('_', ' ', $request->travel_order_type) : 'All',
                    'Status Filter' => $request->status ?: 'All',
                ]);
            }

            $availableColumns = [
                'tracking_code' => 'Tracking Code',
                'picto_no' => 'PICTO No',
                'number' => 'Number',
                'subject' => 'Subject',
                'originating_office' => 'Originating Office',
                'outgoing_type' => 'Type Direction',
                'status' => 'Status',
                'date_received' => 'Date Received',
            ];

            $rows = $query->get()->map(function ($doc) {
                return [
                    'tracking_code' => $doc->dts_number,
                    'picto_no' => $doc->doc_number ?? '—',
                    'number' => $doc->memorandum_number ?? '—',
                    'subject' => $doc->subject ?? '—',
                    'originating_office' => $doc->originatingOffice->code ?? '—',
                    'outgoing_type' => match ($doc->direction) {
                        'INCOMING' => 'Incoming',
                        'OUTGOING' => 'Outgoing',
                        default => '—',
                    },
                    'status' => $doc->status,
                    'date_received' => $doc->date_received ? $doc->date_received->format('F d, Y') : ($doc->created_at ? $doc->created_at->format('F d, Y') : '—'),
                ];
            })->all();

            $visibleKeys = TableExport::normalizeVisibleColumns($request->get('visible_columns'), $availableColumns);
            [$headers, $printRows] = TableExport::projectRows($availableColumns, $rows, $visibleKeys);

            $responseMethod = $exportMode === 'pdf' ? 'pdfTable' : 'printTable';

            return TableExport::{$responseMethod}('Documents', $headers, $printRows, [
                'Search' => $request->search ?: 'All records',
                'Direction' => $request->direction ?: 'All',
                'Document Type' => $request->type ?: 'All',
                'Status Filter' => $request->status ?: 'All',
            ]);
        }

        $query->with(['pins' => fn ($pinQuery) => $pinQuery->where('user_id', auth()->id())]);

        $documents = $query->paginate(15)->withQueryString();
        $offices = Office::ordered()->get();
        $savedFilters = SavedFilter::where('user_id', auth()->id())
            ->where('module', $isTravelOrderPage ? 'travel_orders' : 'documents')
            ->latest()
            ->get();

        return view('documents.index', compact('documents', 'offices', 'isTravelOrderPage', 'savedFilters'));
    }

    public function create(Request $request)
    {
        $offices = Office::ordered()->get();
        $users = User::all();
        $isTravelOrder = $request->query('document_type') === 'TO';
        return view('documents.create', compact('offices', 'users', 'isTravelOrder'));
    }

    private function generateTrackingCode(string $type, int $originOfficeId, $date = null): string
    {
        $documentDate = $date ? \Carbon\Carbon::parse($date) : now();
        $year = $documentDate->year;
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        do {
            $randomString = '';
            for ($i = 0; $i < 12; $i++) {
                $randomString .= $characters[random_int(0, strlen($characters) - 1)];
            }

            $trackingCode = $year . $randomString;
        } while (Document::where('dts_number', $trackingCode)->exists());

        return $trackingCode;
    }

    private function generateTransactionNumber(string $type, int $originOfficeId, $date = null): string
    {
        return DB::transaction(function() use ($type, $originOfficeId, $date) {
            // Use document date if provided, otherwise use current date
            $documentDate = $date ? \Carbon\Carbon::parse($date) : now();
            $year = $documentDate->year;

            // Get originating office code
            $office = Office::find($originOfficeId);
            $officeCode = $office ? $office->code : 'UNKNOWN';

            // Format: PICTO-{ORIGINATING OFFICE}-{TYPE}-{YEAR}-{SEQUENCE}
            $prefix = "PICTO-{$officeCode}-{$type}-{$year}-";

            $lastDoc = Document::where('doc_number', 'like', $prefix . '%')
                ->orderBy('doc_number', 'desc')
                ->lockForUpdate()
                ->first();

            $nextSeq = 1;
            if ($lastDoc) {
                $parts = explode('-', $lastDoc->doc_number);
                $nextSeq = intval(end($parts)) + 1;
            }

            return $prefix . str_pad($nextSeq, 6, '0', STR_PAD_LEFT);
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:MEMO,EO,SO,LETTER,SP,TO,OTHERS',
            'direction' => 'required|in:INCOMING,OUTGOING',
            'delivery_scope' => 'nullable|in:EXTERNAL,INTERNAL|required_if:direction,OUTGOING',
            'originating_office' => 'required|exists:offices,id',
            'date_received' => 'required|date',
            'subject' => 'required|string',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
            'travel_order_type' => 'nullable|in:' . implode(',', self::TRAVEL_ORDER_TYPES),
            'travel_dates' => 'nullable|string|max:255',
            'travelers' => 'nullable|string',
            'destinations' => 'nullable|string',
        ]);

        $isTravelOrder = $request->document_type === 'TO';
        $subject = $isTravelOrder
            ? trim((string) ($request->particulars ?: $request->subject ?: 'Travel Order'))
            : $request->subject;

        $duplicates = $this->findPotentialDuplicates($request);
        if ($duplicates->isNotEmpty() && !$request->boolean('force_save_duplicate')) {
            return back()
                ->withInput()
                ->with('duplicate_warning', 'Possible duplicate documents were found. Please review them before saving.')
                ->with('duplicate_candidates', $duplicates);
        }

        $trackingCode = $this->generateTrackingCode($request->document_type, $request->originating_office, $request->date_received);
        $transactionNumber = $this->generateTransactionNumber($request->document_type, $request->originating_office, $request->date_received);

        $document = Document::create([
            'dts_number' => $trackingCode,
            'picto_number' => null,
            'doc_number' => $transactionNumber,
            'memorandum_number' => $request->memorandum_number,
            'period' => $request->period,
            'document_type' => $request->document_type,
            'direction' => $isTravelOrder ? 'OUTGOING' : $request->direction,
            'delivery_scope' => $isTravelOrder ? 'INTERNAL' : ($request->direction === 'OUTGOING' ? $request->delivery_scope : null),
            'travel_order_type' => $isTravelOrder ? $request->travel_order_type : null,
            'travel_dates' => $isTravelOrder ? $request->travel_dates : null,
            'travelers' => $isTravelOrder ? $request->travelers : null,
            'destinations' => $isTravelOrder ? $request->destinations : null,
            'originating_office' => $request->originating_office,
            'to_office' => $request->to_office,
            'current_office' => $request->originating_office,
            'current_holder' => auth()->id(),
            'subject' => $subject,
            'particulars' => $request->particulars,
            'action_required' => $request->action_required,
            'endorsed_to' => $request->endorsed_to,
            'date_received' => $request->date_received,
            'status' => 'ONGOING',
            'remarks' => $request->remarks,
            'shared_drive_link' => $request->shared_drive_link,
            'received_via_online' => $request->boolean('received_via_online'),
            'encoded_by' => auth()->id(),
            'opg_reference_no' => $request->opg_reference_no,
            'opa_reference_no' => $request->opa_reference_no,
            'governors_instruction' => $request->governors_instruction,
            'administrators_instruction' => $request->administrators_instruction,
            'returned' => $request->returned,
            'opg_action_slip' => $request->opg_action_slip,
            'dts_no' => $request->dts_no,
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('documents/' . $document->id, 'public');
                DocumentFile::create([
                    'document_id' => $document->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        // Create initial routing entry to record document creation
        $document->routes()->create([
            'from_office' => $request->originating_office,
            'to_office' => $request->originating_office,
            'released_by' => auth()->id(),
            'datetime_released' => now(),
            'datetime_received' => now(),
            'received_by' => auth()->id(),
            'remarks' => 'Document created and initially recorded',
        ]);

        app(ActivityLogService::class)->log(
            $document,
            'created',
            'Document created',
            auth()->user()?->name . ' created this document.',
            [
                'status' => $document->status,
                'dts_number' => $document->dts_number,
            ]
        );

        return redirect()
            ->route('documents.index', $isTravelOrder ? ['type' => 'TO'] : [])
            ->with('success', 'Document recorded successfully - Tracking Code: ' . $trackingCode . ', Transaction Number: ' . $transactionNumber);
    }

    public function show(Document $document)
    {
        $this->authorize('view', $document);
        $document->load([
            'originatingOffice',
            'destinationOffice',
            'currentOffice',
            'holder',
            'encoder',
            'routes.fromOffice',
            'routes.toOffice',
            'routes.releasedByUser',
            'routes.receivedByUser',
            'files',
            'comments.user',
            'comments.children.user',
            'activityLogs.user',
            'approval.requester',
            'approval.reviewer',
            'pins',
        ]);
        $offices = Office::ordered()->get();
        $users = User::all();
        $isTravelOrder = $document->document_type === 'TO';
        $exportMode = request()->get('export');

        if ($exportMode === 'csv') {
            return TableExport::csv('document-' . $document->id . '.csv', ['Tracking Code', 'PICTO No', 'Number', 'Document Type', 'Direction', 'Type Direction', 'Subject', 'Particulars', 'Originating Office', 'Date Received', 'Action Required', 'Endorsed To', 'Current Office', 'Current Holder', 'Status', 'Remarks'], [[
                $document->dts_number,
                $document->doc_number ?? '—',
                $document->memorandum_number ?? '—',
                $document->document_type,
                $document->direction,
                match ($document->direction) {
                    'INCOMING' => 'Incoming',
                    'OUTGOING' => 'Outgoing',
                    default => '—',
                },
                $document->subject ?? '—',
                $document->particulars ?? '—',
                $document->originatingOffice->name ?? '—',
                $document->date_received ? $document->date_received->format('F d, Y') : '—',
                $document->action_required ?? '—',
                $document->endorsed_to ?? '—',
                $document->currentOffice->code ?? '—',
                $document->holder->name ?? '—',
                $document->status,
                $document->remarks ?? '—',
            ]]);
        }

        if (in_array($exportMode, ['print', 'pdf'], true)) {
            $sections = [
                [
                    'title' => 'Document Information',
                    'fields' => [
                        'Tracking Code' => $document->dts_number,
                        'PICTO No' => $document->doc_number ?? '—',
                        'Number' => $document->memorandum_number ?? '—',
                        'Document Type' => $document->document_type,
                        'Direction' => $document->direction,
                        'Type Direction' => match ($document->direction) {
                            'INCOMING' => 'Incoming',
                            'OUTGOING' => 'Outgoing',
                            default => '—',
                        },
                        'Subject' => $document->subject ?? '—',
                        'Particulars' => $document->particulars ?? '—',
                        'Originating Office' => $document->originatingOffice->name ?? '—',
                        'Date Received' => $document->date_received ? $document->date_received->format('F d, Y') : '—',
                        'Action Required' => $document->action_required ?? '—',
                        'Endorsed To' => $document->endorsed_to ?? '—',
                        'Current Office' => $document->currentOffice->code ?? '—',
                        'Current Holder' => $document->holder->name ?? '—',
                        'Status' => $document->status,
                        'Encoded By' => $document->encoder->name ?? '—',
                        'Received Online' => $document->received_via_online ? 'Yes' : 'No',
                        'Remarks' => $document->remarks ?? '—',
                    ],
                ],
            ];

            if ($isTravelOrder) {
                $sections[] = [
                    'title' => 'Travel Order Details',
                    'fields' => [
                        'Travel Order Type' => $document->travel_order_type ? str_replace('_', ' ', $document->travel_order_type) : '—',
                        'Date/s of Travel' => $document->travel_dates ?? '—',
                        'Destination/s' => $document->destinations ?? '—',
                        'Name/s' => preg_replace("/\r\n|\r|\n/", ', ', $document->travelers ?? '—'),
                    ],
                ];
            }

            $responseMethod = $exportMode === 'pdf' ? 'pdfRecord' : 'printRecord';

            return TableExport::{$responseMethod}($isTravelOrder ? 'Travel Order Details' : 'Document Details', $sections, [
                'Generated' => now()->format('F d, Y h:i A'),
                'Record ID' => $document->id,
            ]);
        }

        return view('documents.show', compact('document', 'offices', 'users', 'isTravelOrder'));
    }

    public function edit(Document $document)
    {
        $this->authorize('update', $document);
        
        $offices = Office::ordered()->get();
        $users = User::all();
        $isTravelOrder = $document->document_type === 'TO';
        return view('documents.edit', compact('document', 'offices', 'users', 'isTravelOrder'));
    }

    public function update(Request $request, Document $document)
    {
        $this->authorize('update', $document);
        if ($blocked = $this->ensureDocumentActionAllowed($document)) {
            return $blocked;
        }

        $request->validate([
            'document_type' => 'required|in:MEMO,EO,SO,LETTER,SP,TO,OTHERS',
            'direction' => 'required|in:INCOMING,OUTGOING',
            'delivery_scope' => 'nullable|in:EXTERNAL,INTERNAL|required_if:direction,OUTGOING',
            'originating_office' => 'required|exists:offices,id',
            'date_received' => 'required|date',
            'subject' => 'required|string',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
            'travel_order_type' => 'nullable|in:' . implode(',', self::TRAVEL_ORDER_TYPES),
            'travel_dates' => 'nullable|string|max:255',
            'travelers' => 'nullable|string',
            'destinations' => 'nullable|string',
        ]);

        $isTravelOrder = $request->document_type === 'TO';
        $subject = $isTravelOrder
            ? trim((string) ($request->particulars ?: $request->subject ?: 'Travel Order'))
            : $request->subject;

        $duplicates = $this->findPotentialDuplicates($request, $document);
        if ($duplicates->isNotEmpty() && !$request->boolean('force_save_duplicate')) {
            return back()
                ->withInput()
                ->with('duplicate_warning', 'Possible duplicate documents were found. Please review them before saving.')
                ->with('duplicate_candidates', $duplicates);
        }

        // Check if date changed and regenerate tracking code and transaction number if needed
        $oldDate = $document->date_received ? $document->date_received->format('Y-m-d') : null;
        $newDate = $request->date_received;
        
        $updateData = [
            'document_type' => $request->document_type,
            'direction' => $isTravelOrder ? 'OUTGOING' : $request->direction,
            'delivery_scope' => $isTravelOrder ? 'INTERNAL' : ($request->direction === 'OUTGOING' ? $request->delivery_scope : null),
            'travel_order_type' => $isTravelOrder ? $request->travel_order_type : null,
            'travel_dates' => $isTravelOrder ? $request->travel_dates : null,
            'travelers' => $isTravelOrder ? $request->travelers : null,
            'destinations' => $isTravelOrder ? $request->destinations : null,
            'originating_office' => $request->originating_office,
            'to_office' => $request->to_office,
            'current_office' => $request->originating_office,
            'current_holder' => auth()->id(),
            'subject' => $subject,
            'memorandum_number' => $request->memorandum_number,
            'period' => $request->period,
            'particulars' => $request->particulars,
            'action_required' => $request->action_required,
            'endorsed_to' => $request->endorsed_to,
            'date_received' => $request->date_received,
            'status' => $request->status ?? $document->status,
            'remarks' => $request->remarks,
            'shared_drive_link' => $request->shared_drive_link,
            'received_via_online' => $request->boolean('received_via_online'),
            'opg_reference_no' => $request->opg_reference_no,
            'opa_reference_no' => $request->opa_reference_no,
            'governors_instruction' => $request->governors_instruction,
            'administrators_instruction' => $request->administrators_instruction,
            'returned' => $request->returned,
            'opg_action_slip' => $request->opg_action_slip,
            'dts_no' => $request->dts_no,
        ];

        // Regenerate tracking code if date changed
        if ($oldDate !== $newDate) {
            $updateData['dts_number'] = $this->generateTrackingCode($request->document_type, $request->originating_office, $newDate);
            $codesRegenerated = true;
        } else {
            $codesRegenerated = false;
        }

        // Also update current_office and current_holder if originating office changed
        if ($document->originating_office != $request->originating_office) {
            $updateData['current_office'] = $request->originating_office;
            $updateData['current_holder'] = auth()->id();
            // Regenerate PICTO number when originating office changes
            $updateData['doc_number'] = $this->generateTransactionNumber($request->document_type, $request->originating_office, $document->date_received);
            $codesRegenerated = true;
            
            // Add route entry to track office change
            $document->routes()->create([
                'from_office' => $document->current_office,
                'to_office' => $request->originating_office,
                'released_by' => auth()->id(),
                'datetime_released' => now(),
                'datetime_received' => now(),
                'received_by' => auth()->id(),
                'remarks' => 'Document originating office updated from ' . ($document->originatingOffice->code ?? 'Unknown') . ' to ' . ($request->originating_office ? \App\Models\Office::find($request->originating_office)->code : 'Unknown'),
            ]);
        }

        $previousStatus = $document->status;
        $document->update($updateData);

        app(ActivityLogService::class)->log(
            $document,
            'updated',
            'Document updated',
            auth()->user()?->name . ' updated this document.',
            [
                'status' => $document->status,
                'dts_number' => $document->dts_number,
            ]
        );

        // Add completion entry if status changed to DONE
        if (($updateData['status'] ?? $previousStatus) === 'DONE' && $previousStatus !== 'DONE') {
            $document->routes()->create([
                'from_office' => $updateData['current_office'] ?? $document->current_office,
                'to_office' => $updateData['current_office'] ?? $document->current_office,
                'released_by' => auth()->id(),
                'datetime_released' => now(),
                'datetime_received' => now(),
                'received_by' => auth()->id(),
                'remarks' => 'Document marked as DONE',
            ]);
        }

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('documents', 'public');
                    $document->files()->create([
                        'document_id' => $document->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }
        }

        $successMessage = $codesRegenerated 
            ? 'Document updated successfully. Tracking Code and Transaction Number regenerated due to date change.'
            : 'Document updated successfully.';
            
        return redirect()->route('documents.show', $document)->with('success', $successMessage);
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);
        if ($blocked = $this->ensureDocumentActionAllowed($document, false, 'delete')) {
            return $blocked;
        }

        $isTravelOrder = $document->document_type === 'TO';
        $document->delete();
        return redirect()->route('documents.index', $isTravelOrder ? ['type' => 'TO'] : [])->with('success', 'Document deleted.');
    }

    public function route(Request $request, Document $document)
    {
        $this->authorize('route', $document);
        if ($blocked = $this->ensureDocumentActionAllowed($document)) {
            return $blocked;
        }

        $request->validate([
            'to_office' => 'required|exists:offices,id',
            'remarks' => 'nullable|string',
        ]);

        $document->routes()->create([
            'from_office' => $document->current_office,
            'to_office' => $request->to_office,
            'released_by' => auth()->id(),
            'datetime_released' => now(),
            'remarks' => $request->remarks,
        ]);

        $document->update([
            'current_office' => $request->to_office,
        ]);

        app(ActivityLogService::class)->log(
            $document,
            'forwarded',
            'Document forwarded',
            auth()->user()?->name . ' forwarded this document.',
            [
                'to_office' => $request->to_office,
                'dts_number' => $document->dts_number,
            ]
        );

        app(InAppNotificationService::class)->notifyDocumentForwarded($document->fresh(['currentOffice', 'destinationOffice', 'encoder', 'holder']), (int) $request->to_office, auth()->user());

        return redirect()->route('documents.show', $document)->with('success', 'Document forwarded successfully.');
    }

    public function receive(Request $request, Document $document)
    {
        $this->authorize('receive', $document);
        if ($blocked = $this->ensureDocumentActionAllowed($document)) {
            return $blocked;
        }

        $latestRoute = $document->routes()->whereNull('datetime_received')->latest()->first();

        if ($latestRoute) {
            $latestRoute->update([
                'received_by' => auth()->id(),
                'datetime_received' => now(),
            ]);
        }

        $document->update([
            'current_holder' => auth()->id(),
        ]);

        app(ActivityLogService::class)->log(
            $document,
            'received',
            'Document received',
            auth()->user()?->name . ' received this document.',
            ['dts_number' => $document->dts_number]
        );

        app(InAppNotificationService::class)->notifyDocumentReceived($document->fresh(['encoder', 'holder']), auth()->user());

        return redirect()->route('documents.show', $document)->with('success', 'Document received.');
    }

    public function trackingNumbers()
    {
        $documents = Document::orderBy('document_type')
            ->orderBy('dts_number')
            ->get(['dts_number', 'document_type', 'subject', 'created_at']);

        // Group by document type and year
        $grouped = [];
        foreach ($documents as $doc) {
            $parts = explode('-', $doc->dts_number);
            if (count($parts) >= 4) {
                $type = $parts[1];
                $year = $parts[2];
                $grouped[$type][$year][] = $doc;
            }
        }

        return view('documents.tracking-numbers', compact('grouped'));
    }
}
