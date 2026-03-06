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

        $documents = $query->orderBy('doc_number', 'asc')->paginate(15)->withQueryString();
        $offices = Office::ordered()->get();

        return view('documents.index', compact('documents', 'offices'));
    }

    public function create()
    {
        $offices = Office::ordered()->get();
        $users = User::all();
        return view('documents.create', compact('offices', 'users'));
    }

    private function generateTrackingCode(string $type, int $originOfficeId, $date = null): string
    {
        // Use document date if provided, otherwise use current date
        $documentDate = $date ? \Carbon\Carbon::parse($date) : now();
        $year = $documentDate->year;

        // Generate random string (12 characters)
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < 12; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Format: {YEAR}{RANDOM_STRING}
        return $year . $randomString;
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
            'document_type' => 'required|in:MEMO,EO,SO,LETTER,SP,OTHERS',
            'direction' => 'required|in:INCOMING,OUTGOING',
            'originating_office' => 'required|exists:offices,id',
            'date_received' => 'required|date',
            'subject' => 'required|string',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        $trackingCode = $this->generateTrackingCode($request->document_type, $request->originating_office, $request->date_received);
        $transactionNumber = $this->generateTransactionNumber($request->document_type, $request->originating_office, $request->date_received);

        $document = Document::create([
            'dts_number' => $trackingCode,
            'picto_number' => null,
            'doc_number' => $transactionNumber,
            'memorandum_number' => $request->memorandum_number,
            'document_type' => $request->document_type,
            'direction' => $request->direction,
            'originating_office' => $request->originating_office,
            'to_office' => $request->to_office,
            'current_office' => $request->originating_office,
            'current_holder' => auth()->id(),
            'subject' => $request->subject,
            'particulars' => $request->particulars,
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

        return redirect()->route('documents.index')->with('success', 'Document recorded successfully — Tracking Code: ' . $trackingCode . ', Transaction Number: ' . $transactionNumber);
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
            'date_received' => 'required|date',
            'subject' => 'required|string',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ]);

        // Check if date changed and regenerate tracking code and transaction number if needed
        $oldDate = $document->date_received ? $document->date_received->format('Y-m-d') : null;
        $newDate = $request->date_received;
        
        $updateData = [
            'document_type' => $request->document_type,
            'direction' => $request->direction,
            'originating_office' => $request->originating_office,
            'to_office' => $request->to_office,
            'subject' => $request->subject,
            'memorandum_number' => $request->memorandum_number,
            'particulars' => $request->particulars,
            'action_required' => $request->action_required,
            'endorsed_to' => $request->endorsed_to,
            'date_received' => $request->date_received,
            'status' => $request->status ?? $document->status,
            'remarks' => $request->remarks,
            'shared_drive_link' => $request->shared_drive_link,
            'received_via_online' => $request->boolean('received_via_online'),
        ];

        // Regenerate tracking code only if date changed (PICTO Number should remain the same)
        if ($oldDate !== $newDate) {
            $updateData['dts_number'] = $this->generateTrackingCode($request->document_type, $request->originating_office, $newDate);
            // Note: PICTO Number (doc_number) is NOT regenerated - it stays the same for the document's lifetime
            $codesRegenerated = true;
        } else {
            $codesRegenerated = false;
        }

        $document->update($updateData);

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
