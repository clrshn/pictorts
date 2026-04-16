<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Support\TableExport;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $query = Office::with('users')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->ordered();
        $exportMode = $request->get('export');

        if ($exportMode === 'csv') {
            $rows = $query->get()->map(function ($office) {
                return [
                    $office->code,
                    $office->name,
                    $office->users->count(),
                ];
            })->all();

            return TableExport::csv('offices-report.csv', ['Office Code', 'Office Name', 'Users'], $rows);
        }

        if (in_array($exportMode, ['print', 'pdf'], true)) {
            $availableColumns = [
                'office_code' => 'Office Code',
                'office_name' => 'Office Name',
                'users' => 'Users',
            ];

            $rows = $query->get()->map(function ($office) {
                return [
                    'office_code' => $office->code,
                    'office_name' => $office->name,
                    'users' => $office->users->count(),
                ];
            })->all();

            $visibleKeys = TableExport::normalizeVisibleColumns($request->get('visible_columns'), $availableColumns);
            [$headers, $printRows] = TableExport::projectRows($availableColumns, $rows, $visibleKeys);

            $responseMethod = $exportMode === 'pdf' ? 'pdfTable' : 'printTable';

            return TableExport::{$responseMethod}('Office Management', $headers, $printRows, [
                'Search' => $request->search ?: 'All offices',
            ]);
        }

        $offices = $query->paginate(15)->withQueryString();

        return view('offices.index', compact('offices'));
    }

    public function create()
    {
        $offices = Office::ordered()->get();
        return view('offices.create', compact('offices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:offices,code',
            'name' => 'required|string|max:255',
        ]);

        Office::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
        ]);

        return redirect()->route('offices.index')
            ->with('success', 'Office created successfully.');
    }

    public function edit(Office $office)
    {
        $offices = Office::ordered()
            ->where('id', '!=', $office->id)
            ->get();

        return view('offices.edit', compact('office', 'offices'));
    }

    public function update(Request $request, Office $office)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:offices,code,' . $office->id,
            'name' => 'required|string|max:255',
        ]);

        $office->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
        ]);

        return redirect()->route('offices.index')
            ->with('success', 'Office updated successfully.');
    }

    public function destroy(Office $office)
    {
        // Check if office has users
        if ($office->users()->count() > 0) {
            return redirect()->route('offices.index')
                ->with('error', 'Cannot delete office. It has associated users.');
        }

        $office->delete();

        return redirect()->route('offices.index')
            ->with('success', 'Office deleted successfully.');
    }
}
