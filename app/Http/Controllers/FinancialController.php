<?php

namespace App\Http\Controllers;

use App\Models\FinancialRecord;
use App\Models\FinancialAttachment;
use App\Models\Office;
use App\Models\User;
use App\Support\TableExport;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialRecord::with(['originOffice', 'currentOffice', 'holder']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
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

        // Sort by various options
        if ($request->filled('sort_by')) {
            if ($request->sort_by === 'newest') {
                $query->orderBy('created_at', 'desc');
            } elseif ($request->sort_by === 'oldest') {
                $query->orderBy('created_at', 'asc');
            } elseif ($request->sort_by === 'az') {
                $query->orderBy('description', 'asc');
            } elseif ($request->sort_by === 'za') {
                $query->orderBy('description', 'desc');
            } elseif ($request->sort_by === 'highest') {
                $query->orderBy('pr_amount', 'desc');
            } elseif ($request->sort_by === 'lowest') {
                $query->orderBy('pr_amount', 'asc');
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        if ($request->get('export') === 'csv') {
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

        if ($request->get('export') === 'print') {
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

            return TableExport::printTable('Financial Monitoring', $headers, $printRows, [
                'Search' => $request->search ?: 'All records',
                'Status Filter' => $request->status ?: 'All',
                'Type Filter' => $request->type ?: 'All',
            ]);
        }

        $records = $query->paginate(15)->withQueryString();
        $offices = Office::ordered()->get();

        return view('financial.index', compact('records', 'offices'));
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

        $record = FinancialRecord::create([
            'type' => $request->type,
            'description' => $request->description,
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

        return redirect()->route('financial.index')->with('success', 'Financial record created.');
    }

    public function show(FinancialRecord $financial)
    {
        $this->authorize('view', $financial);
        $financial->load(['originOffice', 'currentOffice', 'holder', 'routes.fromOffice', 'routes.toOffice', 'routes.releasedByUser', 'routes.receivedByUser', 'attachments']);
        $offices = Office::ordered()->get();
        $users = User::all();

        if (request()->get('export') === 'csv') {
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

        if (request()->get('export') === 'print') {
            return TableExport::printRecord('Financial Record Details', [
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

        return view('financial.show', compact('financial', 'offices', 'users'));
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
        $request->validate([
            'description' => 'required|string',
            'office_origin' => 'required|exists:offices,id',
            'pr_amount' => 'nullable|numeric|min:0|max:9999999999.99',
            'po_amount' => 'nullable|numeric|min:0|max:9999999999.99',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        $updateData = [
            'type' => $request->type,
            'description' => $request->description,
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

        // Add completion entry if status changed to COMPLETED
        if (($updateData['status'] ?? $previousStatus) === 'COMPLETED' && $previousStatus !== 'COMPLETED') {
            $financial->routes()->create([
                'from_office' => $updateData['current_office'] ?? $financial->current_office,
                'to_office' => $updateData['current_office'] ?? $financial->current_office,
                'released_by' => auth()->id(),
                'datetime_released' => now(),
                'datetime_received' => now(),
                'received_by' => auth()->id(),
                'remarks' => 'Financial record marked as COMPLETED',
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
        
        $request->validate([
            'status' => 'required|in:ACTIVE,CANCELLED,FINISHED',
        ]);

        $financial->update([
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'status' => $financial->status
        ]);
    }

    public function destroy(FinancialRecord $financial)
    {
        $this->authorize('delete', $financial);
        $financial->delete();
        return redirect()->route('financial.index')->with('success', 'Financial record deleted.');
    }

    public function route(Request $request, FinancialRecord $financial)
    {
        $this->authorize('route', $financial);
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

        return redirect()->route('financial.show', $financial)->with('success', 'Financial record forwarded.');
    }

    public function receive(Request $request, FinancialRecord $financial)
    {
        $this->authorize('receive', $financial);
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

        return redirect()->route('financial.show', $financial)->with('success', 'Financial record received.');
    }
}
