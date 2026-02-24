<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\FinancialRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDocuments = Document::count();
        $incomingCount = Document::where('direction', 'INCOMING')->count();
        $outgoingCount = Document::where('direction', 'OUTGOING')->count();

        $totalFinancial = FinancialRecord::count();
        $financialActive = FinancialRecord::where('status', 'ACTIVE')->count();
        $financialCancelled = FinancialRecord::where('status', 'CANCELLED')->count();
        $financialFinished = FinancialRecord::where('status', 'FINISHED')->count();

        $docOngoing = Document::where('status', 'ONGOING')->count();
        $docDelivered = Document::where('status', 'DELIVERED')->count();
        $docCompleted = Document::where('status', 'COMPLETED')->count();

        // Monthly document counts for the current year
        $monthlyDocs = Document::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyFinancial = FinancialRecord::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->pluck('total', 'month')
            ->toArray();

        // Fill all 12 months
        $chartDocuments = [];
        $chartFinancial = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartDocuments[] = $monthlyDocs[$i] ?? 0;
            $chartFinancial[] = $monthlyFinancial[$i] ?? 0;
        }

        return view('dashboard', compact(
            'totalDocuments', 'incomingCount', 'outgoingCount',
            'totalFinancial', 'financialActive', 'financialCancelled', 'financialFinished',
            'docOngoing', 'docDelivered', 'docCompleted',
            'chartDocuments', 'chartFinancial'
        ))->with('docIncoming', $incomingCount);
    }
}
