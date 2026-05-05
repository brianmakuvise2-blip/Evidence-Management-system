<?php

namespace App\Http\Controllers;

use App\Models\CourtBundle;
use App\Models\Evidence;
use App\Models\TransferRequest;
use App\Models\UserActivityLog;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display reports dashboard.
     */
    public function index(Request $request)
    {
        $reportType = $request->get('report', 'overview');

        $data = [
            'totalEvidence' => Evidence::count(),
            'verifiedEvidence' => Evidence::where('status', Evidence::STATUS_VERIFIED)->count(),
            'transferredEvidence' => Evidence::where('status', Evidence::STATUS_TRANSFERRED)->count(),
            'disclosedEvidence' => Evidence::where('status', Evidence::STATUS_DISCLOSED)->count(),
            'activeCases' => Evidence::distinct('case_reference')->count('case_reference'),
            'pendingTransfers' => TransferRequest::where('status', TransferRequest::STATUS_PENDING)->count(),
            'inTransitTransfers' => TransferRequest::where('status', TransferRequest::STATUS_IN_TRANSIT)->count(),
            'approvedTransfers' => TransferRequest::where('status', TransferRequest::STATUS_APPROVED)->count(),
            'approvedBundles' => CourtBundle::where('status', CourtBundle::STATUS_APPROVED)->count(),
            'draftBundles' => CourtBundle::where('status', CourtBundle::STATUS_DRAFT)->count(),
            'bundleStatusCounts' => CourtBundle::select('status', DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status')->toArray(),
            'transferStatusCounts' => TransferRequest::select('status', DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status')->toArray(),
            'evidenceStatusCounts' => Evidence::select('status', DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status')->toArray(),
            'latestTransfers' => TransferRequest::with(['evidence', 'requestedBy', 'receivingOfficer'])->latest()->limit(8)->get(),
            'latestBundles' => CourtBundle::with('preparedBy')->latest()->limit(8)->get(),
            'recentActivity' => UserActivityLog::with('user')->latest()->limit(10)->get(),
            'reportType' => $reportType,
        ];

        return view('reports.index', compact('data'));
    }

    /**
     * Export report data as PDF.
     */
    public function export(Request $request)
    {
        $reportType = $request->get('report', 'overview');
        $fileName   = sprintf('evidence-report-%s-%s.pdf', $reportType, now()->format('YmdHis'));

        $titles = [
            'overview'  => 'System Overview',
            'evidence'  => 'Evidence Status',
            'transfers' => 'Transfer Status',
            'bundles'   => 'Bundle Status',
            'activity'  => 'Recent Activity',
        ];
        $title = $titles[$reportType] ?? 'Report';

        $rows = match ($reportType) {
            'evidence' => collect(
                Evidence::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')->pluck('count', 'status')->toArray()
            )->map(fn($c, $s) => [Evidence::getStatuses()[$s] ?? $s, $c])->values()->toArray(),

            'transfers' => collect(
                TransferRequest::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')->pluck('count', 'status')->toArray()
            )->map(fn($c, $s) => [TransferRequest::getStatuses()[$s] ?? $s, $c])->values()->toArray(),

            'bundles' => collect(
                CourtBundle::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')->pluck('count', 'status')->toArray()
            )->map(fn($c, $s) => [CourtBundle::getStatuses()[$s] ?? $s, $c])->values()->toArray(),

            'activity' => UserActivityLog::with('user')->latest()->limit(100)->get()
                ->map(fn($log) => [
                    $log->created_at->toDateTimeString(),
                    $log->user->name ?? 'System',
                    $log->action,
                    $log->description ?? '—',
                    ucfirst($log->status),
                ])->toArray(),

            default => [
                ['Total Evidence',      Evidence::count()],
                ['Verified Evidence',   Evidence::where('status', Evidence::STATUS_VERIFIED)->count()],
                ['Transferred Evidence',Evidence::where('status', Evidence::STATUS_TRANSFERRED)->count()],
                ['Disclosed Evidence',  Evidence::where('status', Evidence::STATUS_DISCLOSED)->count()],
                ['Pending Transfers',   TransferRequest::where('status', TransferRequest::STATUS_PENDING)->count()],
                ['Approved Bundles',    CourtBundle::where('status', CourtBundle::STATUS_APPROVED)->count()],
            ],
        };

        $landscape = $reportType === 'activity';
        $subtitle  = 'Generated ' . now()->format('d M Y, H:i');

        $columns = $landscape
            ? [
                ['label' => 'Date & Time',   'width' => 80],
                ['label' => 'User',          'width' => 80],
                ['label' => 'Action',        'width' => 85],
                ['label' => 'Description',   'width' => 130],
                ['label' => 'Status',        'width' => 55],
              ]
            : [
                ['label' => 'Metric / Status', 'width' => 260],
                ['label' => 'Value / Count',   'width' => 100],
              ];

        $pdf = (new PdfService())->build($title, $subtitle, $columns, $rows, $landscape);

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"$fileName\"");
    }
}
