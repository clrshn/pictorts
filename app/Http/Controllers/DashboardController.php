<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\FinancialRecord;
use App\Models\Approval;
use App\Models\Todo;

class DashboardController extends Controller
{
    public function index()
    {
        // The dashboard aggregates multiple modules into one view, so the queries here
        // intentionally stay read-only and summary-focused.
        $totalDocuments = Document::count();
        $incomingCount = Document::where('direction', 'INCOMING')->count();
        $outgoingCount = Document::where('direction', 'OUTGOING')->count();

        $totalFinancial = FinancialRecord::count();
        $financialActive = FinancialRecord::where('status', 'ACTIVE')->count();
        $financialCancelled = FinancialRecord::where('status', 'CANCELLED')->count();
        $financialFinished = FinancialRecord::where('status', 'FINISHED')->count();

        $todoPending = Todo::whereIn('status', ['pending', 'on-going'])->count();
        $todoDueToday = Todo::whereDate('due_date', now()->toDateString())
            ->whereIn('status', ['pending', 'on-going'])
            ->count();
        $todoDueTomorrow = Todo::whereDate('due_date', now()->addDay()->toDateString())
            ->whereIn('status', ['pending', 'on-going'])
            ->count();
        $todoDueThisWeek = Todo::whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->whereIn('status', ['pending', 'on-going'])
            ->count();
        $todoOverdue = Todo::whereDate('due_date', '<', now()->toDateString())
            ->whereIn('status', ['pending', 'on-going'])
            ->count();
        $docOngoing = Document::where('status', 'ONGOING')->count();
        $docDelivered = Document::where('status', 'DELIVERED')->count();
        $docCompleted = Document::where('status', 'DONE')->count();
        $approvalPendingCount = Approval::where('status', Approval::STATUS_PENDING)->count();

        // Monthly chart data is normalized to all 12 months so the frontend chart
        // always renders a full-year timeline even when some months have zero records.
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
            'todoPending', 'todoDueToday', 'todoDueTomorrow', 'todoDueThisWeek', 'todoOverdue',
            'docOngoing', 'docDelivered', 'docCompleted',
            'approvalPendingCount',
            'chartDocuments', 'chartFinancial'
        ))->with('docIncoming', $incomingCount);
    }
}
