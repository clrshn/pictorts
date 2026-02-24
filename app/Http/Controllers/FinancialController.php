<?php

namespace App\Http\Controllers;

use App\Models\FinancialRecord;
use App\Models\FinancialAttachment;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialRecord::with(['originOffice', 'currentOffice', 'holder']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        $records = $query->latest()->paginate(15)->withQueryString();
        $offices = Office::all();

        return view('financial.index', compact('records', 'offices'));
    }

    public function create()
    {
        $offices = Office::all();
        return view('financial.create', compact('offices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'office_origin' => 'required|exists:offices,id',
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
            'status' => 'ACTIVE',
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

        return redirect()->route('financial.index')->with('success', 'Financial record created.');
    }

    public function show(FinancialRecord $financial)
    {
        $financial->load(['originOffice', 'currentOffice', 'holder', 'routes.fromOffice', 'routes.toOffice', 'routes.releasedByUser', 'routes.receivedByUser', 'attachments']);
        $offices = Office::all();
        $users = User::all();
        return view('financial.show', compact('financial', 'offices', 'users'));
    }

    public function edit(FinancialRecord $financial)
    {
        $offices = Office::all();
        return view('financial.edit', compact('financial', 'offices'));
    }

    public function update(Request $request, FinancialRecord $financial)
    {
        $request->validate([
            'description' => 'required|string',
            'office_origin' => 'required|exists:offices,id',
        ]);

        $financial->update([
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
            'status' => $request->status ?? $financial->status,
            'remarks' => $request->remarks,
        ]);

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

    public function destroy(FinancialRecord $financial)
    {
        $financial->delete();
        return redirect()->route('financial.index')->with('success', 'Financial record deleted.');
    }

    public function route(Request $request, FinancialRecord $financial)
    {
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
