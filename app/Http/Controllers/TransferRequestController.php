<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use App\Models\TransferRequest;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferRequestController extends Controller
{
    /**
     * Show all transfer requests
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = TransferRequest::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by role/permission
        if ($user->hasPermissionTo('approve-transfer')) {
            // Supervisors see transfers for their institution
            $query->where(function ($q) use ($user) {
                $q->where('destination_institution_id', $user->institution_id)
                  ->orWhereHas('requestedBy', function ($userQ) use ($user) {
                      $userQ->where('institution_id', $user->institution_id);
                  });
            });
        } elseif ($user->hasPermissionTo('request-transfer')) {
            // Officers see only their own requests
            $query->where('requested_by_user_id', $user->id);
        }

        $transfers = $query->with([
            'evidence',
            'requestedBy',
            'receivingOfficer',
            'supervisorApprover',
            'acknowledgedBy',
            'destinationInstitution'
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(20)
        ->withQueryString();

        return view('transfers.index', compact('transfers'));
    }

    /**
     * Show create transfer form
     */
    public function create(Request $request)
    {
        $evidenceId = $request->query('evidence_id');
        $evidence = null;

        if ($evidenceId) {
            $evidence = Evidence::findOrFail($evidenceId);
            
            // Users can only request transfer of evidence in their institution
            if ($evidence->evidence()->institution_id !== Auth::user()->institution_id) {
                abort(403, 'You do not have permission to transfer this evidence.');
            }
        }

        $institutions = Institution::where('id', '!=', Auth::user()->institution_id)
            ->orderBy('name')
            ->get();

        return view('transfers.create', compact('evidence', 'institutions'));
    }

    /**
     * Store transfer request
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Verify user can request transfers
        if (!$user->hasPermissionTo('request-transfer')) {
            abort(403, 'You do not have permission to request transfers.');
        }

        $validated = $request->validate([
            'evidence_id' => 'required|exists:evidence,id',
            'receiving_officer_id' => 'required|exists:users,id',
            'destination_institution_id' => 'required|exists:institutions,id',
            'transfer_reason' => 'required|string|min:10|max:500',
            'urgency_level' => 'required|in:low,medium,high,critical',
        ]);

        // Check if receiving officer is from destination institution and is active
        $receivingOfficer = User::findOrFail($validated['receiving_officer_id']);
        if ($receivingOfficer->institution_id != $validated['destination_institution_id']) {
            return back()->withErrors(['receiving_officer_id' => 'Selected officer is not from the destination institution.']);
        }

        if ($receivingOfficer->account_status !== 'active') {
            return back()->withErrors(['receiving_officer_id' => 'Selected officer account is not active.']);
        }

        // Check evidence exists and belongs to user's institution
        $evidence = Evidence::findOrFail($validated['evidence_id']);
        if ($evidence->institution_id !== $user->institution_id) {
            abort(403, 'Evidence does not belong to your institution.');
        }

        // Check evidence is in appropriate status for transfer
        if (! in_array($evidence->status, [Evidence::STATUS_VERIFIED, Evidence::STATUS_STORED, Evidence::STATUS_TRANSFERRED])) {
            return back()->withErrors(['evidence_id' => 'Evidence must be verified or stored to be transferred.']);
        }

        // Check no pending transfer already exists
        if (TransferRequest::where('evidence_id', $evidence->id)
            ->where('status', TransferRequest::STATUS_PENDING)
            ->exists()) {
            return back()->withErrors(['evidence_id' => 'A pending transfer request already exists for this evidence.']);
        }

        try {
            DB::beginTransaction();

            $transferRequest = TransferRequest::create([
                'evidence_id' => $evidence->id,
                'requested_by_user_id' => $user->id,
                'receiving_officer_id' => $receivingOfficer->id,
                'destination_institution_id' => $validated['destination_institution_id'],
                'transfer_reason' => $validated['transfer_reason'],
                'urgency_level' => $validated['urgency_level'],
                'status' => TransferRequest::STATUS_PENDING,
                'requested_at' => now(),
                'transfer_reference' => TransferRequest::generateTransferReference(),
            ]);

            // Log activity
            $user->logActivity('transfer_requested', 'info', [
                'evidence_id' => $evidence->id,
                'transfer_reference' => $transferRequest->transfer_reference,
                'destination_institution' => Institution::find($validated['destination_institution_id'])->name ?? 'Unknown',
                'urgency' => $validated['urgency_level'],
            ]);

            DB::commit();

            return redirect()
                ->route('transfers.show', $transferRequest)
                ->with('success', 'Transfer request created successfully (Reference: ' . $transferRequest->transfer_reference . ')');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create transfer request: ' . $e->getMessage()]);
        }
    }

    /**
     * Show transfer request detail
     */
    public function show(TransferRequest $transfer)
    {
        $user = Auth::user();

        // Check authorization
        if (!$user->hasPermissionTo('approve-transfer') &&
            !$user->hasPermissionTo('acknowledge-receipt') &&
            $transfer->requested_by_user_id !== $user->id) {
            abort(403);
        }

        $transfer->load([
            'evidence',
            'requestedBy',
            'receivingOfficer',
            'supervisorApprover',
            'acknowledgedBy',
            'destinationInstitution',
        ]);

        // Get custody history for this evidence
        $custodyHistory = $transfer->evidence->chainOfCustody()
            ->orderBy('transferred_at', 'asc')
            ->get();

        return view('transfers.show', compact('transfer', 'custodyHistory'));
    }

    /**
     * Show supervisor approval form
     */
    public function approvalForm(TransferRequest $transfer)
    {
        $user = Auth::user();

        // Only supervisors can approve
        if (!$user->hasPermissionTo('approve-transfer')) {
            abort(403);
        }

        // Check if user's institution matches
        if ($transfer->requestedBy->institution_id !== $user->institution_id &&
            $transfer->destination_institution_id !== $user->institution_id) {
            abort(403);
        }

        $transfer->load(['evidence', 'requestedBy', 'receivingOfficer', 'destinationInstitution']);

        return view('transfers.approval-form', compact('transfer'));
    }

    /**
     * Approve transfer request
     */
    public function approve(Request $request, TransferRequest $transfer)
    {
        $user = Auth::user();

        // Only supervisors can approve
        if (!$user->hasPermissionTo('approve-transfer')) {
            abort(403);
        }

        if ($transfer->requested_by_user_id === $user->id) {
            return back()->with('error', 'You cannot approve your own transfer request.');
        }

        // SECURITY: Prevent users who collected the evidence from approving transfers
        $evidence = $transfer->evidence;
        if ($evidence && $evidence->collected_by_user_id === $user->id) {
            return back()->with('error', 'You cannot approve transfers of evidence you collected. This violates separation of duties.');
        }

        // Validation
        $validated = $request->validate([
            'approval_notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $transfer->approve($user, $validated['approval_notes'] ?? null);

            DB::commit();

            return redirect()
                ->route('transfers.show', $transfer)
                ->with('success', 'Transfer approved. Evidence is now in transit.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show rejection form
     */
    public function rejectionForm(TransferRequest $transfer)
    {
        $user = Auth::user();

        // Only supervisors can reject
        if (!$user->hasPermissionTo('approve-transfer')) {
            abort(403);
        }

        $transfer->load(['evidence', 'requestedBy', 'receivingOfficer']);

        return view('transfers.rejection-form', compact('transfer'));
    }

    /**
     * Reject transfer request
     */
    public function reject(Request $request, TransferRequest $transfer)
    {
        $user = Auth::user();

        // Only supervisors can reject
        if (!$user->hasPermissionTo('approve-transfer')) {
            abort(403);
        }

        if ($transfer->requested_by_user_id === $user->id) {
            return back()->with('error', 'You cannot reject your own transfer request.');
        }

        // Validation
        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
            'rejection_correction_notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $transfer->reject($user, $validated['rejection_reason'], $validated['rejection_correction_notes'] ?? null);

            DB::commit();

            return redirect()
                ->route('transfers.show', $transfer)
                ->with('warning', 'Transfer request rejected. Correction notes sent to requesting officer.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show receipt acknowledgment form
     */
    public function acknowledgmentForm(TransferRequest $transfer)
    {
        $user = Auth::user();

        // Only designated receiving officer can acknowledge
        if ($transfer->receiving_officer_id !== $user->id) {
            abort(403);
        }

        // Only in-transit transfers can be acknowledged
        if ($transfer->status !== TransferRequest::STATUS_IN_TRANSIT) {
            abort(403, 'This transfer is not in transit.');
        }

        $transfer->load(['evidence', 'requestedBy', 'receivingOfficer', 'supervisorApprover', 'destinationInstitution']);

        return view('transfers.acknowledgment-form', compact('transfer'));
    }

    /**
     * Acknowledge receipt
     */
    public function acknowledgeReceipt(Request $request, TransferRequest $transfer)
    {
        $user = Auth::user();

        // Only designated receiving officer can acknowledge
        if ($transfer->receiving_officer_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'acknowledgment_notes' => 'nullable|string|max:500',
            'condition_verification' => 'required|in:intact,damaged,compromised',
        ]);

        try {
            DB::beginTransaction();

            $notes = "Condition: {$validated['condition_verification']}";
            if (!empty($validated['acknowledgment_notes'])) {
                $notes .= "\nNotes: {$validated['acknowledgment_notes']}";
            }

            $transfer->acknowledgeReceipt($user, $notes);

            DB::commit();

            return redirect()
                ->route('transfers.show', $transfer)
                ->with('success', 'Receipt acknowledged successfully. Transfer is complete.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show custody history for evidence
     */
    public function custodyHistory(Evidence $evidence)
    {
        $user = Auth::user();

        // Verify access to evidence
        $hasInstitutionAccess = $evidence->institution_id === $user->institution_id;
        $hasGlobalAccess = $user->hasPermissionTo('view-all-evidence');
        $hasRelatedTransferAccess = TransferRequest::where('evidence_id', $evidence->id)
            ->where(function ($query) use ($user) {
                $query->where('requested_by_user_id', $user->id)
                    ->orWhere('receiving_officer_id', $user->id)
                    ->orWhere('destination_institution_id', $user->institution_id)
                    ->orWhereHas('requestedBy', function ($subQuery) use ($user) {
                        $subQuery->where('institution_id', $user->institution_id);
                    });
            })
            ->exists();

        if (! $hasInstitutionAccess && ! $hasGlobalAccess && ! $hasRelatedTransferAccess) {
            return redirect()->back()->with('error', 'You are not authorized to view the full custody history for this evidence.');
        }

        $custodyHistory = $evidence->chainOfCustody()
            ->with(['fromUser', 'toUser', 'fromInstitution', 'toInstitution', 'supervisorApprover'])
            ->orderBy('transferred_at', 'asc')
            ->get();

        return view('transfers.custody-history', compact('evidence', 'custodyHistory'));
    }

    /**
     * Export custody history (for legal proceedings)
     */
    public function exportCustodyHistory(Evidence $evidence)
    {
        $user = Auth::user();

        // Only authorized roles can export
        if (!$user->hasPermissionTo('disclose-evidence')) {
            abort(403);
        }

        $custodyHistory = $evidence->chainOfCustody()
            ->with(['fromUser', 'toUser', 'fromInstitution', 'toInstitution', 'supervisorApprover'])
            ->orderBy('transferred_at', 'asc')
            ->get();

        $filename = "custody_history_exhibit_{$evidence->exhibit_number}_" . now()->format('YmdHis') . ".pdf";

        // For now, return CSV export - can be extended to PDF
        $csv = "CUSTODY HISTORY REPORT\n";
        $csv .= "Evidence: " . $evidence->exhibit_number . "\n";
        $csv .= "Generated: " . now()->format('Y-m-d H:i:s') . "\n";
        $csv .= str_repeat("=", 120) . "\n\n";
        
        $csv .= "Transfer #,Date/Time,From Institution,From Officer,To Institution,To Officer,Reason,Reference,Status\n";

        foreach ($custodyHistory as $index => $record) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s,%s,Transferred\n",
                $index + 1,
                $record->transferred_at?->format('Y-m-d H:i:s') ?? 'N/A',
                $record->fromInstitution?->name ?? 'Unknown',
                $record->fromUser?->name ?? 'Unknown',
                $record->toInstitution?->name ?? 'Unknown',
                $record->toUser?->name ?? 'Unknown',
                $record->transfer_reason ?? 'N/A',
                $record->transfer_reference ?? 'N/A'
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}
