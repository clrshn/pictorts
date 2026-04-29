<?php

namespace App\Http\Controllers;

use App\Models\FinancialRecord;
use App\Models\FinancialAttachment;
use App\Models\Office;
use App\Models\SavedFilter;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\InAppNotificationService;
use App\Support\TableExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FinancialController extends Controller
{
    private function ensureFinancialActionAllowed(FinancialRecord $financial, bool $expectsJson = false, string $action = 'update')
    {
        // Financial records use the same approval lock principle as documents so
        // reviewed records are not changed accidentally by regular users.
        $approval = $financial->approval;

        if (!auth()->user()?->isAdmin() && $approval?->status === 'pending') {
            return $expectsJson
                ? response()->json(['success' => false, 'message' => 'This financial record has a pending approval request and is temporarily locked.'], 422)
                : redirect()->back()->with('warning', 'This financial record has a pending approval request and is temporarily locked.');
        }

        if (!auth()->user()?->isAdmin() && in_array($action, ['update', 'delete'], true) && $approval?->status === 'approved') {
            return $expectsJson
                ? response()->json(['success' => false, 'message' => 'This financial record is already approved. Only an admin can modify it now.'], 422)
                : redirect()->back()->with('warning', 'This financial record is already approved. Only an admin can modify it now.');
        }

        return null;
    }

    private function generateReferenceCode(?string $date = null): string
    {
        // Reference codes are generated inside a transaction and with row locking to
        // reduce the chance of duplicate numbers during concurrent creation.
        return DB::transaction(function () use ($date) {
            $year = ($date ? Carbon::parse($date) : now())->format('Y');
            $prefix = "PICTO-FIN-{$year}-";

            $lastRecord = FinancialRecord::query()
                ->where('reference_code', 'like', $prefix . '%')
                ->orderByDesc('reference_code')
                ->lockForUpdate()
                ->first();

            $next = 1;
            if ($lastRecord?->reference_code) {
                $next = ((int) substr($lastRecord->reference_code, -6)) + 1;
            }

            return $prefix . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
        });
    }

    private function findPotentialDuplicates(Request $request, ?FinancialRecord $ignore = null)
    {
        // Duplicate checks are intentionally broad because financial records are often
        // identified by one or more reference numbers, supplier, and description.
        $description = trim((string) $request->input('description'));
        $supplier = trim((string) $request->input('supplier'));
        $hasReferenceFields = $description !== ''
            || $request->filled('pr_number')
            || $request->filled('po_number')
            || $request->filled('obr_number')
            || $request->filled('voucher_number');

        if (!$hasReferenceFields) {
            return collect();
        }

        $query = FinancialRecord::query()
            ->when($ignore, fn ($q) => $q->whereKeyNot($ignore->id))
            ->where(function ($q) use ($request, $description, $supplier) {
                if ($description !== '') {
                    $q->orWhereRaw('LOWER(description) = ?', [strtolower($description)]);
                }

                if ($request->filled('pr_number')) {
                    $q->orWhere('pr_number', $request->pr_number);
                }

                if ($request->filled('po_number')) {
                    $q->orWhere('po_number', $request->po_number);
                }

                if ($request->filled('obr_number')) {
                    $q->orWhere('obr_number', $request->obr_number);
                }

                if ($request->filled('voucher_number')) {
                    $q->orWhere('voucher_number', $request->voucher_number);
                }

                if ($description !== '' && $supplier !== '' && $request->filled('type')) {
                    $q->orWhere(function ($inner) use ($description, $supplier, $request) {
                        $inner->whereRaw('LOWER(description) = ?', [strtolower($description)])
                            ->whereRaw('LOWER(COALESCE(supplier, "")) = ?', [strtolower($supplier)])
                            ->where('type', $request->type);
                    });
                }
            })
            ->latest()
            ->limit(5);

        return $query->get();
    }

    public function index(Request $request)
    {
        // Like the document module, the financial listing is also the source for
        // exports and reports, so filtering and sorting are kept in one place.
        $query = FinancialRecord::with(['originOffice', 'currentOffice', 'holder']);
        $exportMode = $request->get('export');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->boolean('pinned_only')) {
            $query->whereHas('pins', fn ($pinQuery) => $pinQuery->where('user_id', auth()->id()));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                  ->orWhere('reference_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%")
                  ->orWhere('pr_number', 'like', "%{$search}%")
                  ->orWhere('po_number', 'like', "%{$search}%")
                  ->orWhere('obr_number', 'like', "%{$search}%")
                  ->orWhere('voucher_number', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhereHas('originOffice', function ($oq) use ($search) {
                      $oq->where('code', 'like', "%{$search}%")
                          ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('sort_by')) {
            switch ($request->sort_by) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'az':
                case 'description_az':
                    $query->orderBy('description', 'asc');
                    break;
                case 'za':
                case 'description_za':
                    $query->orderBy('description', 'desc');
                    break;
                case 'highest':
                case 'pr_highest':
                    $query->orderBy('pr_amount', 'desc');
                    break;
                case 'lowest':
                case 'pr_lowest':
                    $query->orderBy('pr_amount', 'asc');
                    break;
                case 'po_highest':
                    $query->orderBy('po_amount', 'desc');
                    break;
                case 'po_lowest':
                    $query->orderBy('po_amount', 'asc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        if ($exportMode === 'csv') {
            $rows = $query->get()->map(function ($record) {
                return [
                    $record->status,
                    $record->type ?? '—',
                    $record->description ?? '—',
                    $record->supplier ?? '—',
                    $record->pr_amount ?? 0,
                    $record->pr_number ?? '—',
                    $record->po_amount ?? 0,
                    $record->po_number ?? '—',
                    $record->obr_number ?? '—',
                    $record->voucher_number ?? '—',
                    $record->originOffice?->code ?? '—',
                    $record->progress ?? '—',
                    $record->remarks ?? '—',
                ];
            })->all();

            return TableExport::csv('financial-report.csv', ['Status', 'Type', 'Description', 'Supplier', 'PR Amount', 'PR #', 'PO Amount', 'PO #', 'OBR #', 'Voucher #', 'Office Origin', 'Progress', 'Remarks'], $rows);
        }

        if (in_array($exportMode, ['print', 'pdf'], true)) {
            $availableColumns = [
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
            ];

            $rows = $query->get()->map(function ($record) {
                return [
                    'status' => $record->status,
                    'type' => $record->type ?? '—',
                    'description' => $record->description ?? '—',
                    'supplier' => $record->supplier ?? '—',
                    'pr_amount' => number_format((float) ($record->pr_amount ?? 0), 2),
                    'pr_number' => $record->pr_number ?? '—',
                    'po_amount' => number_format((float) ($record->po_amount ?? 0), 2),
                    'po_number' => $record->po_number ?? '—',
                    'obr_number' => $record->obr_number ?? '—',
                    'voucher_number' => $record->voucher_number ?? '—',
                    'office_origin' => $record->originOffice?->code ?? '—',
                    'progress' => $record->progress ?? '—',
                ];
            })->all();

            $visibleKeys = TableExport::normalizeVisibleColumns($request->get('visible_columns'), $availableColumns);
            [$headers, $printRows] = TableExport::projectRows($availableColumns, $rows, $visibleKeys);

            $meta = [
                'Search' => $request->search ?: 'All records',
                'Status Filter' => $request->status ?: 'All',
                'Type Filter' => $request->type ?: 'All',
            ];

            return $exportMode === 'pdf'
                ? TableExport::pdfTable('Financial Monitoring', $headers, $printRows, $meta)
                : TableExport::printTable('Financial Monitoring', $headers, $printRows, $meta);
        }

        $query->with(['pins' => fn ($pinQuery) => $pinQuery->where('user_id', auth()->id())]);

        $records = $query->paginate(15)->withQueryString();
        $offices = Office::ordered()->get();
        $savedFilters = SavedFilter::where('user_id', auth()->id())
            ->where('module', 'financial')
            ->latest()
            ->get();

        return view('financial.index', compact('records', 'offices', 'savedFilters'));
    }

    public function create()
    {
        $offices = Office::ordered()->get();
        return view('financial.create', compact('offices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'office_origin' => 'required|exists:offices,id',
            'pr_amount' => 'nullable|numeric|min:0|max:9999999999.99',
            'po_amount' => 'nullable|numeric|min:0|max:9999999999.99',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        $duplicates = $this->findPotentialDuplicates($request);
        if ($duplicates->isNotEmpty() && !$request->boolean('force_save_duplicate')) {
            return back()
                ->withInput()
                ->with('duplicate_warning', 'Possible duplicate financial records were found. Please review them before saving.')
                ->with('duplicate_candidates', $duplicates);
        }

        $record = FinancialRecord::create([
            'type' => $request->type,
            'description' => $request->description,
            'reference_code' => $this->generateReferenceCode(),
            'supplier' => $request->supplier,
            'pr_number' => $request->pr_number,
            'pr_amount' => $request->pr_amount,
            'po_number' => $request->po_number,
            'po_amount' => $request->po_amount,
            'obr_number' => $request->obr_number,
            'voucher_number' => $request->voucher_number,
            'office_origin' => $request->office_origin,
            'current_office' => $request->office_origin,
            'current_holder' => auth()->id(),
            'created_by' => auth()->id(),
            'status' => $request->status,
            'progress' => $request->progress,
            'remarks' => $request->remarks,
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('financial/' . $record->id, 'public');
                FinancialAttachment::create([
                    'financial_id' => $record->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        // Create initial routing entry to record financial record creation
        $record->routes()->create([
            'from_office' => $request->office_origin,
            'to_office' => $request->office_origin,
            'released_by' => auth()->id(),
            'datetime_released' => now(),
            'datetime_received' => now(),
            'received_by' => auth()->id(),
            'remarks' => 'Financial record created and initially recorded',
        ]);

        app(ActivityLogService::class)->log(
            $record,
            'created',
            'Financial record created',
            auth()->user()?->name . ' created this financial record.',
            [
                'status' => $record->status,
                'type' => $record->type,
            ]
        );

        return redirect()->route('financial.index')->with('success', 'Financial record created.');
    }

    public function show(FinancialRecord $financial)
    {
        $this->authorize('view', $financial);
        $financial->load([
            'originOffice',
            'currentOffice',
            'holder',
            'createdBy',
            'routes.fromOffice',
            'routes.toOffice',
            'routes.releasedByUser',
            'routes.receivedByUser',
            'attachments',
            'comments.user',
            'comments.children.user',
            'activityLogs.user',
            'approval.requester',
            'approval.reviewer',
            'pins',
        ]);
        $supplierHistory = filled($financial->supplier)
            ? $financial->relatedSupplierRecords()->with('originOffice')->get()
            : collect();
        $offices = Office::ordered()->get();
        $users = User::all();
        $exportMode = request()->get('export');

        if ($exportMode === 'csv') {
            return TableExport::csv('financial-record-' . $financial->id . '.csv', ['Type', 'Description', 'Supplier', 'Office', 'Current Office', 'Current Holder', 'Status', 'Progress', 'PR Number', 'PR Amount', 'PO Number', 'PO Amount', 'OBR Number', 'Voucher Number', 'Remarks'], [[
                $financial->type ?? '—',
                $financial->description ?? '—',
                $financial->supplier ?? '—',
                trim(($financial->originOffice->code ?? '—') . ' - ' . ($financial->originOffice->name ?? '')),
                $financial->currentOffice->code ?? '—',
                $financial->holder->name ?? '—',
                $financial->status,
                $financial->progress ?? '—',
                $financial->pr_number ?? '—',
                $financial->pr_amount ?? 0,
                $financial->po_number ?? '—',
                $financial->po_amount ?? 0,
                $financial->obr_number ?? '—',
                $financial->voucher_number ?? '—',
                $financial->remarks ?? '—',
            ]]);
        }

        if (in_array($exportMode, ['print', 'pdf'], true)) {
            $responseMethod = $exportMode === 'pdf' ? 'pdfRecord' : 'printRecord';

            return TableExport::{$responseMethod}('Financial Record Details', [
                [
                    'title' => 'Record Information',
                    'fields' => [
                        'Type' => $financial->type ?? '—',
                        'Description' => $financial->description ?? '—',
                        'Supplier' => $financial->supplier ?? '—',
                        'Office Origin' => trim(($financial->originOffice->code ?? '—') . ' - ' . ($financial->originOffice->name ?? '')),
                        'Current Office' => $financial->currentOffice->code ?? '—',
                        'Current Holder' => $financial->holder->name ?? '—',
                        'Status' => $financial->status,
                        'Progress' => $financial->progress ?? '—',
                        'PR Number' => $financial->pr_number ?? '—',
                        'PR Amount' => $financial->pr_amount ? number_format((float) $financial->pr_amount, 2) : '—',
                        'PO Number' => $financial->po_number ?? '—',
                        'PO Amount' => $financial->po_amount ? number_format((float) $financial->po_amount, 2) : '—',
                        'OBR Number' => $financial->obr_number ?? '—',
                        'Voucher Number' => $financial->voucher_number ?? '—',
                        'Remarks' => $financial->remarks ?? '—',
                    ],
                ],
            ], [
                'Generated' => now()->format('F d, Y h:i A'),
                'Record ID' => $financial->id,
            ]);
        }

        return view('financial.show', compact('financial', 'offices', 'users', 'supplierHistory'));
    }

    public function previewFile(FinancialRecord $financial, FinancialAttachment $file)
    {
        $this->authorize('view', $financial);
        abort_unless($file->financial_id === $financial->id, 404);
        abort_unless(Storage::disk('public')->exists($file->file_path), 404);

        $absolutePath = Storage::disk('public')->path($file->file_path);
        $mimeType = Storage::disk('public')->mimeType($file->file_path) ?: 'application/octet-stream';

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . addslashes($file->file_name) . '"',
        ]);
    }

    public function downloadFile(FinancialRecord $financial, FinancialAttachment $file)
    {
        $this->authorize('view', $financial);
        abort_unless($file->financial_id === $financial->id, 404);
        abort_unless(Storage::disk('public')->exists($file->file_path), 404);

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    public function edit(FinancialRecord $financial)
    {
        $this->authorize('update', $financial);
        $offices = Office::ordered()->get();
        return view('financial.edit', compact('financial', 'offices'));
    }

    public function update(Request $request, FinancialRecord $financial)
    {
        $this->authorize('update', $financial);
        if ($blocked = $this->ensureFinancialActionAllowed($financial)) {
            return $blocked;
        }

        $request->validate([
            'description' => 'required|string',
            'office_origin' => 'required|exists:offices,id',
            'pr_amount' => 'nullable|numeric|min:0|max:9999999999.99',
            'po_amount' => 'nullable|numeric|min:0|max:9999999999.99',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        $duplicates = $this->findPotentialDuplicates($request, $financial);
        if ($duplicates->isNotEmpty() && !$request->boolean('force_save_duplicate')) {
            return back()
                ->withInput()
                ->with('duplicate_warning', 'Possible duplicate financial records were found. Please review them before saving.')
                ->with('duplicate_candidates', $duplicates);
        }

        $updateData = [
            'type' => $request->type,
            'description' => $request->description,
            'reference_code' => $financial->reference_code ?: $this->generateReferenceCode(),
            'supplier' => $request->supplier,
            'pr_number' => $request->pr_number,
            'pr_amount' => $request->pr_amount,
            'po_number' => $request->po_number,
            'po_amount' => $request->po_amount,
            'obr_number' => $request->obr_number,
            'voucher_number' => $request->voucher_number,
            'office_origin' => $request->office_origin,
            'current_office' => $request->office_origin,
            'current_holder' => auth()->id(),
            'status' => $request->status ?? $financial->status,
            'progress' => $request->progress,
            'remarks' => $request->remarks,
        ];

        // Add route entry if office origin changed
        if ($financial->office_origin != $request->office_origin) {
            $financial->routes()->create([
                'from_office' => $financial->current_office,
                'to_office' => $request->office_origin,
                'released_by' => auth()->id(),
                'datetime_released' => now(),
                'datetime_received' => now(),
                'received_by' => auth()->id(),
                'remarks' => 'Financial record office origin updated from ' . ($financial->originOffice->code ?? 'Unknown') . ' to ' . ($request->office_origin ? \App\Models\Office::find($request->office_origin)->code : 'Unknown'),
            ]);
        }

        $previousStatus = $financial->status;
        $financial->update($updateData);

        app(ActivityLogService::class)->log(
            $financial,
            'updated',
            'Financial record updated',
            auth()->user()?->name . ' updated this financial record.',
            [
                'status' => $financial->status,
                'type' => $financial->type,
            ]
        );

        // Add completion entry if status changed to FINISHED
        if (($updateData['status'] ?? $previousStatus) === 'FINISHED' && $previousStatus !== 'FINISHED') {
            $financial->routes()->create([
                'from_office' => $updateData['current_office'] ?? $financial->current_office,
                'to_office' => $updateData['current_office'] ?? $financial->current_office,
                'released_by' => auth()->id(),
                'datetime_released' => now(),
                'datetime_received' => now(),
                'received_by' => auth()->id(),
                'remarks' => 'Financial record marked as FINISHED',
            ]);
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('financial/' . $financial->id, 'public');
                FinancialAttachment::create([
                    'financial_id' => $financial->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('financial.show', $financial)->with('success', 'Financial record updated.');
    }

    public function updateStatus(Request $request, FinancialRecord $financial)
    {
        $this->authorize('update', $financial);
        if ($blocked = $this->ensureFinancialActionAllowed($financial, true)) {
            return $blocked;
        }
        
        $request->validate([
            'status' => 'required|in:ACTIVE,CANCELLED,FINISHED',
        ]);

        $financial->update([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        app(ActivityLogService::class)->log(
            $financial,
            'status_changed',
            'Financial status changed',
            auth()->user()?->name . ' changed the financial status.',
            ['status' => $financial->status]
        );

        app(InAppNotificationService::class)->notifyFinancialStatusChanged($financial->fresh(['createdBy', 'holder']), auth()->user());

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'status' => $financial->status
        ]);
    }

    public function destroy(FinancialRecord $financial)
    {
        $this->authorize('delete', $financial);
        if ($blocked = $this->ensureFinancialActionAllowed($financial, false, 'delete')) {
            return $blocked;
        }

        $financial->delete();
        return redirect()->route('financial.index')->with('success', 'Financial record deleted.');
    }

    public function route(Request $request, FinancialRecord $financial)
    {
        $this->authorize('route', $financial);
        if ($blocked = $this->ensureFinancialActionAllowed($financial)) {
            return $blocked;
        }

        $request->validate([
            'to_office' => 'required|exists:offices,id',
        ]);

        $financial->routes()->create([
            'from_office' => $financial->current_office,
            'to_office' => $request->to_office,
            'released_by' => auth()->id(),
            'datetime_released' => now(),
            'remarks' => $request->remarks,
        ]);

        $financial->update([
            'current_office' => $request->to_office,
        ]);

        app(ActivityLogService::class)->log(
            $financial,
            'forwarded',
            'Financial record forwarded',
            auth()->user()?->name . ' forwarded this financial record.',
            ['to_office' => $request->to_office]
        );

        app(InAppNotificationService::class)->notifyFinancialForwarded($financial->fresh(['createdBy', 'holder', 'currentOffice']), (int) $request->to_office, auth()->user());

        return redirect()->route('financial.show', $financial)->with('success', 'Financial record forwarded.');
    }

    public function receive(Request $request, FinancialRecord $financial)
    {
        $this->authorize('receive', $financial);
        if ($blocked = $this->ensureFinancialActionAllowed($financial)) {
            return $blocked;
        }

        $latestRoute = $financial->routes()->whereNull('datetime_received')->latest()->first();

        if ($latestRoute) {
            $latestRoute->update([
                'received_by' => auth()->id(),
                'datetime_received' => now(),
            ]);
        }

        $financial->update([
            'current_holder' => auth()->id(),
        ]);

        app(ActivityLogService::class)->log(
            $financial,
            'received',
            'Financial record received',
            auth()->user()?->name . ' received this financial record.'
        );

        app(InAppNotificationService::class)->notifyFinancialReceived($financial->fresh(['createdBy', 'holder']), auth()->user());

        return redirect()->route('financial.show', $financial)->with('success', 'Financial record received.');
    }
}
