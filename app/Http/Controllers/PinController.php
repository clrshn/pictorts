<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\FinancialRecord;
use App\Models\Todo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PinController extends Controller
{
    public function store(Request $request)
    {
        $record = $this->resolveRecord($request->input('subject_type'), (int) $request->input('subject_id'));

        abort_unless($record, 404);

        $record->pins()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Record pinned successfully.');
    }

    public function destroy(Request $request)
    {
        $record = $this->resolveRecord($request->input('subject_type'), (int) $request->input('subject_id'));

        abort_unless($record, 404);

        $record->pins()->where('user_id', auth()->id())->delete();

        return back()->with('success', 'Record unpinned successfully.');
    }

    private function resolveRecord(?string $type, int $id): ?Model
    {
        return match ($type) {
            'todo' => Todo::find($id),
            'document' => Document::find($id),
            'financial' => FinancialRecord::find($id),
            default => null,
        };
    }
}
