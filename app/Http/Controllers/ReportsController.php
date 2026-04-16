<?php

namespace App\Http\Controllers;

use App\Models\CourtBundle;
use App\Models\Evidence;
use App\Models\TransferRequest;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
     * Export report data as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $reportType = $request->get('report', 'overview');
        $fileName = sprintf('evidence-report-%s-%s.csv', $reportType, now()->format('YmdHis'));

        return response()->streamDownload(function () use ($reportType) {
            $handle = fopen('php://output', 'w');

            switch ($reportType) {
                case 'evidence':
                    fputcsv($handle, ['Status', 'Count']);
                    foreach (Evidence::select('status', DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status')->toArray() as $status => $count) {
                        fputcsv($handle, [Evidence::getStatuses()[$status] ?? $status, $count]);
                    }
                    break;
                case 'transfers':
                    fputcsv($handle, ['Status', 'Count']);
                    foreach (TransferRequest::select('status', DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status')->toArray() as $status => $count) {
                        fputcsv($handle, [TransferRequest::getStatuses()[$status] ?? $status, $count]);
                    }
                    break;
                case 'bundles':
                    fputcsv($handle, ['Status', 'Count']);
                    foreach (CourtBundle::select('status', DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status')->toArray() as $status => $count) {
                        fputcsv($handle, [CourtBundle::getStatuses()[$status] ?? $status, $count]);
                    }
                    break;
                case 'activity':
                    fputcsv($handle, ['Date', 'User', 'Action', 'Description', 'Status']);
                    foreach (UserActivityLog::with('user')->latest()->limit(100)->get() as $log) {
                        fputcsv($handle, [
                            $log->created_at->toDateTimeString(),
                            $log->user->name ?? 'System',
                            $log->action,
                            $log->description,
                            ucfirst($log->status),
                        ]);
                    }
                    break;
                default:
                    fputcsv($handle, ['Metric', 'Value']);
                    fputcsv($handle, ['Total Evidence', Evidence::count()]);
                    fputcsv($handle, ['Verified Evidence', Evidence::where('status', Evidence::STATUS_VERIFIED)->count()]);
                    fputcsv($handle, ['Transferred Evidence', Evidence::where('status', Evidence::STATUS_TRANSFERRED)->count()]);
                    fputcsv($handle, ['Disclosed Evidence', Evidence::where('status', Evidence::STATUS_DISCLOSED)->count()]);
                    fputcsv($handle, ['Pending Transfers', TransferRequest::where('status', TransferRequest::STATUS_PENDING)->count()]);
                    fputcsv($handle, ['Approved Bundles', CourtBundle::where('status', CourtBundle::STATUS_APPROVED)->count()]);
                    break;
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
