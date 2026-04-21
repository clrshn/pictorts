<?php

namespace App\Http\Controllers;

use App\Models\SavedFilter;
use Illuminate\Http\Request;

class SavedFilterController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'module' => 'required|string|max:50',
            'name' => 'required|string|max:100',
            'filters' => 'required|array',
        ]);

        SavedFilter::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'module' => $validated['module'],
                'name' => $validated['name'],
            ],
            [
                'filters' => array_filter($validated['filters'], fn ($value) => $value !== null && $value !== ''),
            ]
        );

        return back()->with('success', 'Filter saved successfully.');
    }

    public function destroy(SavedFilter $savedFilter)
    {
        abort_unless($savedFilter->user_id === auth()->id(), 403);

        $savedFilter->delete();

        return back()->with('success', 'Saved filter removed successfully.');
    }
}
