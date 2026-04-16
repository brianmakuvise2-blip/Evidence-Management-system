<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CourtBundle;
use App\Models\Evidence;
use App\Models\TransferRequest;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard based on user role
     */
    public function index()
    {
        $user = Auth::user();
        
        // Load user with roles and relationships
        $user->load('roles', 'institution', 'department', 'activityLogs');

        // Get different data based on user role
        if ($user->hasRole('system-administrator')) {
            return $this->systemAdminDashboard($user);
        } 
        elseif ($user->hasRole('source-officer')) {
            return $this->sourceOfficerDashboard($user);
        }
        elseif ($user->hasRole('investigator')) {
            return $this->investigatorDashboard($user);
        }
        elseif ($user->hasRole('supervisor')) {
            return $this->supervisorDashboard($user);
        }
        elseif ($user->hasRole('financial-verifier')) {
            return $this->financialVerifierDashboard($user);
        }
        elseif ($user->hasRole('prosecutor')) {
            return $this->prosecutorDashboard($user);
        }
        elseif ($user->hasRole('judicial-viewer')) {
            return $this->judicialViewerDashboard($user);
        }
        elseif ($user->hasRole('administrator')) {
            return $this->adminDashboard($user);
        } 
        elseif ($user->hasRole('evidence-officer')) {
            return $this->evidenceOfficerDashboard($user);
        } 
        else {
            return $this->userDashboard($user);
        }
    }

    /**
     * System Administrator Dashboard
     * Full access to all systems
     */
    private function systemAdminDashboard($user)
    {
        $data = [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('account_status', 'active')->count(),
            'inactiveUsers' => User::where('account_status', 'inactive')->count(),
            'totalRoles' => \Spatie\Permission\Models\Role::count(),
            'totalEvidence' => Evidence::count(),
            'evidenceStatusCounts' => Evidence::select('status', DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status')->toArray(),
            'totalCases' => Evidence::distinct('case_reference')->count('case_reference'),
            'pendingTransfers' => TransferRequest::where('status', TransferRequest::STATUS_PENDING)->count(),
            'overdueTransfers' => TransferRequest::where('status', TransferRequest::STATUS_PENDING)->where('requested_at', '<', now()->subDays(5))->count(),
            'bundleStatusCounts' => CourtBundle::select('status', DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status')->toArray(),
            'recentLogs' => UserActivityLog::with('user')->latest()->limit(10)->get(),
            'recentBundles' => CourtBundle::latest()->limit(5)->get(),
            'dashboardType' => 'system-admin',
        ];

        return view('dashboard.system-admin', compact('user', 'data'));
    }

    /**
     * Administrator Dashboard
     * Can manage users and view admin functions
     */
    private function adminDashboard($user)
    {
        $data = [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('account_status', 'active')->count(),
            'inactiveUsers' => User::where('account_status', 'inactive')->count(),
            'totalEvidence' => Evidence::count(),
            'pendingTransfers' => TransferRequest::where('status', TransferRequest::STATUS_PENDING)->count(),
            'approvedBundles' => CourtBundle::where('status', CourtBundle::STATUS_APPROVED)->count(),
            'recentLogs' => UserActivityLog::with('user')->latest()->limit(5)->get(),
            'dashboardType' => 'admin',
        ];

        return view('dashboard.admin', compact('user', 'data'));
    }

    /**
     * Evidence Officer Dashboard
     * Can manage evidence and access chain of custody
     */
    private function evidenceOfficerDashboard($user)
    {
        $data = [
            'userInfo' => $user,
            'lastLogin' => $user->last_login_at,
            'department' => $user->department,
            'dashboardType' => 'evidence-officer',
        ];

        return view('dashboard.evidence-officer', compact('user', 'data'));
    }

    /**
     * Source Officer Dashboard
     * Can create evidence, view own cases, request transfers
     */
    private function sourceOfficerDashboard($user)
    {
        // Get evidence created by this user
        $myEvidence = Evidence::where('collected_by_user_id', $user->id)
            ->with('institution', 'department')
            ->latest()
            ->limit(10)
            ->get();

        // Get transfer requests initiated by this user
        $myTransferRequests = TransferRequest::where('requested_by_user_id', $user->id)
            ->with('evidence', 'receivingOfficer', 'destinationInstitution')
            ->latest()
            ->limit(5)
            ->get();

        $data = [
            'myEvidence' => $myEvidence,
            'myTransferRequests' => $myTransferRequests,
            'evidenceCount' => $myEvidence->count(),
            'pendingTransfers' => $myTransferRequests->where('status', TransferRequest::STATUS_PENDING)->count(),
            'completedTransfers' => $myTransferRequests->where('status', TransferRequest::STATUS_COMPLETED)->count(),
            'dashboardType' => 'source-officer',
        ];

        return view('dashboard.source-officer', compact('user', 'data'));
    }

    /**
     * Investigator Dashboard
     * Can view assigned cases, request transfers, view chain of custody
     */
    private function investigatorDashboard($user)
    {
        // Get evidence assigned to this investigator's institution/department
        $assignedEvidence = Evidence::where('institution_id', $user->institution_id)
            ->with('institution', 'department', 'collectedBy')
            ->latest()
            ->limit(15)
            ->get();

        // Get transfer requests this investigator can see
        $transferRequests = TransferRequest::where(function($query) use ($user) {
            $query->where('requested_by_user_id', $user->id)
                  ->orWhere('destination_institution_id', $user->institution_id);
        })
        ->with('evidence', 'requestedBy', 'receivingOfficer', 'destinationInstitution')
        ->latest()
        ->limit(10)
        ->get();

        $data = [
            'assignedEvidence' => $assignedEvidence,
            'transferRequests' => $transferRequests,
            'evidenceCount' => $assignedEvidence->count(),
            'pendingRequests' => $transferRequests->where('status', TransferRequest::STATUS_PENDING)->count(),
            'dashboardType' => 'investigator',
        ];

        return view('dashboard.investigator', compact('user', 'data'));
    }

    /**
     * Supervisor Dashboard
     * Can see pending approvals, approve/reject transfers
     */
    private function supervisorDashboard($user)
    {
        // Get pending transfer requests for approval
        $pendingApprovals = TransferRequest::where('status', TransferRequest::STATUS_PENDING)
            ->where(function($query) use ($user) {
                $query->where('requested_by_user_id', '!=', $user->id) // Cannot approve own requests
                      ->where(function($subQuery) use ($user) {
                          $subQuery->where('destination_institution_id', $user->institution_id)
                                   ->orWhereHas('requestedBy', function($userQ) use ($user) {
                                       $userQ->where('institution_id', $user->institution_id);
                                   });
                      });
            })
            ->with('evidence', 'requestedBy', 'receivingOfficer', 'destinationInstitution')
            ->latest()
            ->limit(20)
            ->get();

        // Get recent approvals/rejections by this supervisor
        $recentActions = TransferRequest::where('supervisor_approver_id', $user->id)
            ->where('status', '!=', TransferRequest::STATUS_PENDING)
            ->with('evidence', 'requestedBy')
            ->latest()
            ->limit(10)
            ->get();

        $data = [
            'pendingApprovals' => $pendingApprovals,
            'recentActions' => $recentActions,
            'pendingCount' => $pendingApprovals->count(),
            'approvedCount' => $recentActions->where('status', TransferRequest::STATUS_APPROVED)->count(),
            'rejectedCount' => $recentActions->where('status', TransferRequest::STATUS_REJECTED)->count(),
            'dashboardType' => 'supervisor',
        ];

        return view('dashboard.supervisor', compact('user', 'data'));
    }

    /**
     * Financial Verifier Dashboard
     * Can view financial evidence, verify financial records
     */
    private function financialVerifierDashboard($user)
    {
        // Get financial-related evidence
        $financialEvidence = Evidence::where('institution_id', $user->institution_id)
            ->where(function($query) {
                $query->where('evidence_type', 'financial')
                      ->orWhere('title', 'like', '%financial%')
                      ->orWhere('title', 'like', '%banking%')
                      ->orWhere('title', 'like', '%currency%')
                      ->orWhere('title', 'like', '%money%')
                      ->orWhere('description', 'like', '%financial%')
                      ->orWhere('description', 'like', '%banking%')
                      ->orWhere('description', 'like', '%currency%');
            })
            ->with('institution', 'department', 'collectedBy')
            ->latest()
            ->limit(15)
            ->get();

        $data = [
            'financialEvidence' => $financialEvidence,
            'evidenceCount' => $financialEvidence->count(),
            'verifiedCount' => $financialEvidence->where('status', Evidence::STATUS_VERIFIED)->count(),
            'pendingCount' => $financialEvidence->where('status', Evidence::STATUS_REGISTERED)->count(),
            'dashboardType' => 'financial-verifier',
        ];

        return view('dashboard.financial-verifier', compact('user', 'data'));
    }

    /**
     * Prosecutor Dashboard
     * Can review evidence, prepare court bundles
     */
    private function prosecutorDashboard($user)
    {
        // Get evidence assigned to prosecutor
        $assignedEvidence = Evidence::where('institution_id', $user->institution_id)
            ->where('status', Evidence::STATUS_VERIFIED)
            ->with('institution', 'department', 'collectedBy')
            ->latest()
            ->limit(15)
            ->get();

        // Get court bundles created by this prosecutor
        $myBundles = CourtBundle::where('prepared_by_user_id', $user->id)
            ->with('preparedBy')
            ->latest()
            ->limit(10)
            ->get();

        $data = [
            'assignedEvidence' => $assignedEvidence,
            'myBundles' => $myBundles,
            'evidenceCount' => $assignedEvidence->count(),
            'bundleCount' => $myBundles->count(),
            'approvedBundles' => $myBundles->where('status', CourtBundle::STATUS_APPROVED)->count(),
            'draftBundles' => $myBundles->where('status', CourtBundle::STATUS_DRAFT)->count(),
            'dashboardType' => 'prosecutor',
        ];

        return view('dashboard.prosecutor', compact('user', 'data'));
    }

    /**
     * Judicial Viewer Dashboard
     * Read-only access to approved court bundles
     */
    private function judicialViewerDashboard($user)
    {
        // Get approved court bundles shared with judiciary
        $sharedBundles = CourtBundle::where('status', CourtBundle::STATUS_APPROVED)
            ->whereHas('disclosures', function($query) use ($user) {
                $query->where('shared_with_user_id', $user->id);
            })
            ->with('preparedBy', 'approvedBy')
            ->latest()
            ->limit(10)
            ->get();

        $data = [
            'sharedBundles' => $sharedBundles,
            'bundleCount' => $sharedBundles->count(),
            'recentBundles' => $sharedBundles->take(5),
            'dashboardType' => 'judicial-viewer',
        ];

        return view('dashboard.judicial-viewer', compact('user', 'data'));
    }
}
