<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with(['originatingOffice', 'destinationOffice', 'currentOffice', 'holder']);

        // Filter by document type tab
        if ($request->filled('type') && $request->type !== 'ALL') {
            $query->where('document_type', $request->type);
        }

        // Filter by direction
        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
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
                  ->orWhere('endorsed_to', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhereHas('originatingOffice', function ($oq) use ($search) {
                      $oq->where('code', 'like', "%{$search}%")
                          ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $documents = $query->latest()->paginate(15)->withQueryString();
        $offices = Office::ordered()->get();

        return view('documents.index', compact('documents', 'offices'));
    }

    public function create()
    {
        $offices = Office::ordered()->get();
        $users = User::all();
        return view('documents.create', compact('offices', 'users'));
    }

    private function generateDtsNumber(string $type, int $originOfficeId): string
    {
        return DB::transaction(function() use ($type, $originOfficeId) {
            $office = Office::find($originOfficeId);
            $officeCode = $office ? $office->code : 'PICTO';
            $year = now()->year;

            // Format: PICTO-OFFICE-TYPE-YEAR-SEQUENCE
            $prefix = "PICTO-{$officeCode}-{$type}-{$year}-";

            $lastDoc = Document::where('dts_number', 'like', $prefix . '%')
                ->orderBy('dts_number', 'desc')
                ->lockForUpdate()
                ->first();

            $nextSeq = 1;
            if ($lastDoc) {
                $parts = explode('-', $lastDoc->dts_number);
                $nextSeq = intval(end($parts)) + 1;
            }

            return $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:MEMO,EO,SO,LETTER,SP,OTHERS',
            'direction' => 'required|in:INCOMING,OUTGOING',
            'originating_office' => 'required|exists:offices,id',
            'subject' => 'required|string',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        $dtsNumber = $this->generateDtsNumber($request->document_type, $request->originating_office);

        $document = Document::create([
            'dts_number' => $dtsNumber,
            'picto_number' => $request->picto_number,
            'doc_number' => $request->doc_number,
            'document_type' => $request->document_type,
            'direction' => $request->direction,
            'originating_office' => $request->originating_office,
            'to_office' => $request->to_office,
            'current_office' => $request->originating_office,
            'current_holder' => auth()->id(),
            'subject' => $request->subject,
            'action_required' => $request->action_required,
            'endorsed_to' => $request->endorsed_to,
            'date_received' => $request->date_received,
            'status' => 'ONGOING',
            'remarks' => $request->remarks,
            'shared_drive_link' => $request->shared_drive_link,
            'received_via_online' => $request->boolean('received_via_online'),
            'encoded_by' => auth()->id(),
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

        return redirect()->route('documents.index')->with('success', 'Document recorded successfully — ' . $dtsNumber);
    }

    public function show(Document $document)
    {
        $this->authorize('view', $document);
        $document->load(['originatingOffice', 'destinationOffice', 'currentOffice', 'holder', 'encoder', 'routes.fromOffice', 'routes.toOffice', 'routes.releasedByUser', 'routes.receivedByUser', 'files']);
        $offices = Office::ordered()->get();
        $users = User::all();
        return view('documents.show', compact('document', 'offices', 'users'));
    }

    public function edit(Document $document)
    {
        $this->authorize('update', $document);
        $offices = Office::ordered()->get();
        $users = User::all();
        return view('documents.edit', compact('document', 'offices', 'users'));
    }

    public function update(Request $request, Document $document)
    {
        $this->authorize('update', $document);
        $request->validate([
            'document_type' => 'required|in:MEMO,EO,SO,LETTER,SP,OTHERS',
            'direction' => 'required|in:INCOMING,OUTGOING',
            'originating_office' => 'required|exists:offices,id',
            'subject' => 'required|string',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        $document->update([
            'picto_number' => $request->picto_number,
            'doc_number' => $request->doc_number,
            'document_type' => $request->document_type,
            'direction' => $request->direction,
            'originating_office' => $request->originating_office,
            'to_office' => $request->to_office,
            'subject' => $request->subject,
            'action_required' => $request->action_required,
            'endorsed_to' => $request->endorsed_to,
            'date_received' => $request->date_received,
            'status' => $request->status ?? $document->status,
            'remarks' => $request->remarks,
            'shared_drive_link' => $request->shared_drive_link,
            'received_via_online' => $request->boolean('received_via_online'),
        ]);

        // Add completion entry if status changed to COMPLETED
        if ($request->status === 'COMPLETED' && $document->status !== 'COMPLETED') {
            $document->routes()->create([
                'from_office' => $document->current_office,
                'to_office' => $document->current_office,
                'released_by' => auth()->id(),
                'datetime_released' => now(),
                'datetime_received' => now(),
                'received_by' => auth()->id(),
                'remarks' => 'Document marked as COMPLETED',
            ]);
        }

        // Handle new file uploads
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

        return redirect()->route('documents.show', $document)->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document deleted.');
    }

    public function route(Request $request, Document $document)
    {
        $this->authorize('route', $document);
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

        return redirect()->route('documents.show', $document)->with('success', 'Document forwarded successfully.');
    }

    public function receive(Request $request, Document $document)
    {
        $this->authorize('receive', $document);
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

        return redirect()->route('documents.show', $document)->with('success', 'Document received.');
    }
}
