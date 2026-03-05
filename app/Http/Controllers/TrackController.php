<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function page()
    {
        return view('track');
    }

    public function search(Request $request)
    {
        $request->validate([
            'tracking_code' => 'required|string|max:50|regex:/^PICTO-[A-Z]+-\d{4}-\d{4}$/',
        ]);

        $code = trim($request->tracking_code);

        $document = Document::with([
            'originatingOffice',
            'destinationOffice',
            'currentOffice',
            'holder',
            'encoder',
            'routes.fromOffice',
            'routes.toOffice',
            'routes.releasedByUser',
            'routes.receivedByUser',
        ])
        ->where(function($query) use ($code) {
            $query->where('dts_number', $code)
                  ->orWhere('picto_number', $code)
                  ->orWhere('doc_number', $code);
        })
        ->first();

        if (!$document) {
            return response()->json([
                'found' => false,
                'message' => 'No document found with that tracking code.',
            ]);
        }

        $routes = $document->routes->map(function ($route) {
            return [
                'date' => $route->datetime_released?->format('M d, Y') ?? '-',
                'time' => $route->datetime_released?->format('h:i A') ?? '-',
                'action' => 'Routed from ' . ($route->fromOffice?->code ?? 'N/A') . ' to ' . ($route->toOffice?->code ?? 'N/A'),
                'received_date' => $route->datetime_received?->format('M d, Y h:i A'),
                'released_by' => $route->releasedByUser?->name ?? '-',
                'received_by' => $route->receivedByUser?->name ?? '-',
                'remarks' => $route->remarks ?? '-',
            ];
        });

        return response()->json([
            'found' => true,
            'document' => [
                'tracking_number' => $document->dts_number,
                'document_type' => $document->document_type,
                'direction' => $document->direction,
                'originating_office' => $document->originatingOffice?->code ?? '-',
                'subject' => $document->subject,
                'remarks' => $document->remarks ?? '-',
                'date' => $document->created_at->format('M d, Y'),
                'date_received' => $document->date_received?->format('M d, Y') ?? '-',
                'current_location' => $document->currentOffice?->code ?? '-',
                'current_holder' => $document->holder?->name ?? '-',
                'status' => $document->status,
                'picto_number' => $document->picto_number ?? '-',
                'doc_number' => $document->doc_number ?? '-',
                'endorsed_to' => $document->endorsed_to ?? '-',
                'action_required' => $document->action_required ?? '-',
            ],
            'routes' => $routes,
        ]);
    }
}
