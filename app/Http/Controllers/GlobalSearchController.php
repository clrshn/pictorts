<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\FinancialRecord;
use App\Models\Todo;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->get('q', ''));
        $results = [
            'todos' => collect(),
            'documents' => collect(),
            'financial' => collect(),
        ];

        if ($query !== '') {
            $results['todos'] = Todo::query()
                ->where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('assigned_to', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            $results['documents'] = Document::query()
                ->where('subject', 'like', "%{$query}%")
                ->orWhere('dts_number', 'like', "%{$query}%")
                ->orWhere('doc_number', 'like', "%{$query}%")
                ->orWhere('particulars', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            $results['financial'] = FinancialRecord::query()
                ->where('description', 'like', "%{$query}%")
                ->orWhere('type', 'like', "%{$query}%")
                ->orWhere('supplier', 'like', "%{$query}%")
                ->orWhere('pr_number', 'like', "%{$query}%")
                ->orWhere('po_number', 'like', "%{$query}%")
                ->limit(10)
                ->get();
        }

        return view('search.index', compact('query', 'results'));
    }
}
