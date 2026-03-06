<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $offices = Office::ordered()->paginate(15);
        return view('offices.index', compact('offices'));
    }

    public function create()
    {
        return view('offices.create');
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
        return view('offices.edit', compact('office'));
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
