<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use App\Models\Institution;
use App\Models\Department;
use App\Models\User;
use App\Models\EvidenceHashHistory;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EvidenceController extends Controller
{
    /**
     * Display a listing of the evidence.
     */
    public function index(Request $request)
    {
        $query = Evidence::query();

        // Apply filters based on request parameters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('evidence_type')) {
            $query->byType($request->evidence_type);
        }

        if ($request->filled('institution_id')) {
            $query->byInstitution($request->institution_id);
        }

        if ($request->filled('department_id')) {
            $query->byDepartment($request->department_id);
        }

        if ($request->filled('case_reference')) {
            $query->byCaseReference($request->case_reference);
        }

        if ($request->filled('exhibit_number')) {
            $query->byExhibitNumber($request->exhibit_number);
        }

        if ($request->filled('file_hash')) {
            $query->byFileHash($request->file_hash);
        }

        if ($request->filled('classification_level')) {
            $query->byClassificationLevel($request->classification_level);
        }

        if ($request->filled('officer_name')) {
            $query->byOfficerName($request->officer_name);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('case_reference', 'like', "%{$search}%")
                  ->orWhere('exhibit_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('file_hash', 'like', "%{$search}%")
                  ->orWhere('classification_level', 'like', "%{$search}%")
                  ->orWhereHas('collectedBy', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('verifiedBy', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Log search activity when filters are used
        if ($request->filled('search') || $request->filled('case_reference') || $request->filled('exhibit_number') || $request->filled('file_hash') || $request->filled('officer_name') || $request->filled('classification_level') || $request->filled('start_date') || $request->filled('end_date') || $request->filled('institution_id')) {
            Auth::user()->logActivity('search_evidence', 'info', [
                'search' => $request->only(['search', 'case_reference', 'exhibit_number', 'file_hash', 'officer_name', 'classification_level', 'status', 'evidence_type', 'institution_id', 'start_date', 'end_date']),
            ]);
        }

        // Get related data for display
        $evidence = $query->with(['institution', 'department', 'collectedBy', 'verifiedBy'])
            ->orderByDesc('collected_date')
            ->paginate(15);

        $institutions = Institution::all();
        $statuses = Evidence::getStatuses();
        $evidenceTypes = Evidence::getEvidenceTypes();

        return view('evidence.index', compact('evidence', 'institutions', 'statuses', 'evidenceTypes'));
    }

    /**
     * Display the specified evidence.
     */
    public function show(Evidence $evidence)
    {
        if (! $evidence->canBeViewedBy(Auth::user())) {
            return redirect()->route('evidence.index')
                ->with('error', 'You are not authorized to view this evidence.');
        }

        $evidence->load(['institution', 'department', 'collectedBy', 'verifiedBy', 'chainOfCustodyRecords']);

        Auth::user()->logActivity('view_evidence', 'info', [
            'evidence_id' => $evidence->id,
        ]);

        return view('evidence.show', compact('evidence'));
    }

    /**
     * Show the form for creating a new evidence.
     */
    public function create()
    {
        $institutions = Institution::all();
        $evidenceTypes = Evidence::getEvidenceTypes();
        $statuses = Evidence::getStatuses();

        return view('evidence.create', compact('institutions', 'evidenceTypes', 'statuses'));
    }

    /**
     * Store a newly created evidence in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'case_reference' => 'nullable|string|max:255',
            'exhibit_number' => 'nullable|string|max:255|unique:evidence,exhibit_number',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'evidence_type' => ['required', Rule::in(array_keys(Evidence::getEvidenceTypes()))],
            'collected_date' => 'required|date',
            'source' => 'nullable|string|max:255',
            'location_found' => 'nullable|string|max:255',
            'classification_level' => ['nullable', Rule::in(['public', 'confidential', 'restricted', 'sealed'])],
            'institution_id' => 'required|exists:institutions,id',
            'department_id' => 'required|exists:departments,id',
            'file' => 'nullable|file|max:102400', // Max 100MB
            'metadata' => 'nullable|json',
        ]);

        // Handle file upload
        $filePath = null;
        $fileType = null;
        $fileSize = null;
        $fileHash = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Calculate SHA-256 hash of the original file before storing
            $fileHash = hash_file('sha256', $file->getRealPath());
            
            // Check for duplicate files
            if (Evidence::where('file_hash', $fileHash)->exists()) {
                return back()->withInput()
                    ->with('error', 'This file has already been uploaded to the system (duplicate detected by hash).');
            }

            $filePath = $this->storeEncryptedEvidenceFile($file);
            $fileType = $file->getMimeType();
            $fileSize = $file->getSize();
        }

        // Create evidence record
        $evidence = Evidence::create([
            'case_reference' => $validated['case_reference'] ?? null,
            'exhibit_number' => $validated['exhibit_number'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'evidence_type' => $validated['evidence_type'],
            'collected_date' => $validated['collected_date'],
            'source' => $validated['source'] ?? null,
            'location_found' => $validated['location_found'] ?? null,
            'classification_level' => $validated['classification_level'] ?? 'restricted',
            'collected_by_user_id' => Auth::id(),
            'institution_id' => $validated['institution_id'],
            'department_id' => $validated['department_id'],
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'file_hash' => $fileHash,
            'status' => Evidence::STATUS_REGISTERED,
            'metadata' => $validated['metadata'] ?? null,
        ]);

        // Create hash history entry for integrity tracking
        $this->createHashHistoryEntry($evidence, 'created', Auth::user(), [
            'action' => 'evidence_registered',
            'notes' => 'Initial evidence registration with integrity hash',
        ]);

        Auth::user()->logActivity('evidence_registered', 'success', [
            'evidence_id' => $evidence->id,
            'file_hash' => $fileHash,
        ]);

        // Send notification for new evidence creation
        $this->notifyAdministratorsOfEvidenceCreation($evidence);

        return redirect()->route('evidence.show', $evidence)
            ->with('success', 'Evidence successfully registered.');
    }

    /**
     * Show the form for editing the specified evidence.
     */
    public function edit(Evidence $evidence)
    {
        // Only allow editing if not verified
        if ($evidence->status === Evidence::STATUS_VERIFIED) {
            return redirect()->route('evidence.show', $evidence)
                ->with('error', 'Cannot edit verified evidence.');
        }

        $institutions = Institution::all();
        $evidenceTypes = Evidence::getEvidenceTypes();
        $statuses = Evidence::getStatuses();

        return view('evidence.edit', compact('evidence', 'institutions', 'evidenceTypes', 'statuses'));
    }

    /**
     * Update the specified evidence in storage.
     */
    public function update(Request $request, Evidence $evidence)
    {
        // Prevent editing verified evidence
        if ($evidence->status === Evidence::STATUS_VERIFIED) {
            return redirect()->route('evidence.show', $evidence)
                ->with('error', 'Cannot edit verified evidence.');
        }

        // Validate data
        $validated = $request->validate([
            'case_reference' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'evidence_type' => ['required', Rule::in(array_keys(Evidence::getEvidenceTypes()))],
            'collected_date' => 'required|date',
            'institution_id' => 'required|exists:institutions,id',
            'department_id' => 'required|exists:departments,id',
            'file' => 'nullable|file|max:102400',
        ]);

        // Handle file update
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($evidence->file_path) {
                Storage::disk('evidence')->delete($evidence->file_path);
            }

            $file = $request->file('file');
            $fileHash = hash_file('sha256', $file->getRealPath());

            if (Evidence::where('file_hash', $fileHash)->where('id', '!=', $evidence->id)->exists()) {
                return back()->withInput()
                    ->with('error', 'This file has already been uploaded to the system (duplicate detected by hash).');
            }

            $filePath = $this->storeEncryptedEvidenceFile($file);
            $validated['file_path'] = $filePath;
            $validated['file_type'] = $file->getMimeType();
            $validated['file_size'] = $file->getSize();
            $validated['file_hash'] = $fileHash;
        }

        // Determine changed fields for audit details
        $changedFields = [];
        $trackableAttributes = [
            'case_reference',
            'title',
            'description',
            'evidence_type',
            'collected_date',
            'institution_id',
            'department_id',
        ];

        foreach ($trackableAttributes as $attribute) {
            if (array_key_exists($attribute, $validated) && $evidence->$attribute != $validated[$attribute]) {
                $changedFields[$attribute] = [
                    'old' => $evidence->$attribute,
                    'new' => $validated[$attribute],
                ];
            }
        }

        if ($request->hasFile('file')) {
            $changedFields['file'] = [
                'old_hash' => $evidence->file_hash,
                'new_hash' => $fileHash,
                'old_path' => $evidence->file_path,
                'new_path' => $filePath,
            ];
        }

        if (empty($changedFields)) {
            $changedFields['note'] = 'Evidence update submitted with no material field changes.';
        }

        // Capture old hash BEFORE update for comparison
        $oldHashEntry = EvidenceHashHistory::getLatestForEvidence($evidence->id);
        $oldContentHash = $oldHashEntry?->content_hash;

        // Update evidence
        $evidence->update($validated);

        // Create new hash history entry — covers ALL changes including title, description, etc.
        $freshEvidence = $evidence->fresh();
        $newHashEntry = $this->createHashHistoryEntry($freshEvidence, 'updated', Auth::user(), [
            'changed_fields' => $changedFields,
            'action' => 'evidence_updated',
            'notes' => 'Evidence record modified',
        ]);

        // Add hash comparison to change log for notification
        $changedFields['_integrity_hash'] = [
            'old_hash' => $oldContentHash,
            'new_hash' => $newHashEntry?->content_hash,
            'hash_changed' => $oldContentHash !== $newHashEntry?->content_hash,
        ];

        Auth::user()->logActivity('evidence_updated', 'success', [
            'evidence_id' => $evidence->id,
            'file_updated' => $request->hasFile('file'),
            'changes' => $changedFields,
        ]);

        $this->notifyAdministratorsOfEvidenceChange($freshEvidence, $changedFields);

        return redirect()->route('evidence.show', $freshEvidence)
            ->with('success', 'Evidence successfully updated.');
    }

    /**
     * Notify system administrators when evidence is modified.
     */
    protected function notifyAdministratorsOfEvidenceChange(Evidence $evidence, array $changes): void
    {
        // Notify super-admin, all system/institution admins
        $administrators = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [
                'super-admin', 'administrator', 'system-administrator',
                'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin',
                'zrp-system-admin', 'judicial-system-admin', 'judicial-courts-admin',
            ]);
        })->get()->unique('id');

        if ($administrators->isEmpty()) {
            return;
        }

        // Build human-readable list of changed fields
        $fieldSummary = [];
        foreach ($changes as $field => $change) {
            if ($field === '_integrity_hash') continue;
            if (is_array($change) && isset($change['old'], $change['new'])) {
                $fieldSummary[] = ucfirst(str_replace('_', ' ', $field))
                    . ': "' . $change['old'] . '" → "' . $change['new'] . '"';
            } elseif ($field === 'file' && is_array($change)) {
                $fieldSummary[] = 'File replaced';
            }
        }

        // Hash comparison block
        $hashInfo = $changes['_integrity_hash'] ?? [];
        $oldHash  = $hashInfo['old_hash'] ?? null;
        $newHash  = $hashInfo['new_hash'] ?? null;
        $hashChanged = $oldHash !== $newHash;

        $message = Auth::user()->name . ' modified evidence "' . $evidence->title
            . '" (Case Ref: ' . ($evidence->case_reference ?? 'N/A') . ', ID: ' . $evidence->id . ').';

        if (!empty($fieldSummary)) {
            $message .= ' Changes: ' . implode('; ', $fieldSummary) . '.';
        }

        if ($hashChanged && $oldHash && $newHash) {
            $message .= ' Integrity hash changed — OLD: ' . substr($oldHash, 0, 20) . '...'
                . ' NEW: ' . substr($newHash, 0, 20) . '...';
        }

        $hashChangeSummary = $this->formatHashChangeSummary($evidence, $changes);
        $hashChangeSummary['old_content_hash'] = $oldHash;
        $hashChangeSummary['new_content_hash'] = $newHash;
        $hashChangeSummary['hash_changed']      = $hashChanged;

        $notificationData = [
            'title'        => '⚠ Evidence Modified: ' . $evidence->title,
            'message'      => $message,
            'action_url'   => route('evidence.show', $evidence),
            'action_text'  => 'Review Changes & Hash History',
            'details'      => [
                'evidence_id'    => $evidence->id,
                'evidence_title' => $evidence->title,
                'case_reference' => $evidence->case_reference,
                'modified_by'    => Auth::user()->name,
                'modified_at'    => now()->toISOString(),
                'changed_fields' => $fieldSummary,
                'old_hash'       => $oldHash,
                'new_hash'       => $newHash,
                'hash_changed'   => $hashChanged,
            ],
            'hash_summary' => $hashChangeSummary,
            'type'         => 'evidence_integrity_change',
        ];

        foreach ($administrators as $admin) {
            $admin->notify(new GeneralNotification($notificationData));
        }

        Auth::user()->logActivity('evidence_integrity_notification_sent', 'info', [
            'evidence_id'             => $evidence->id,
            'notification_recipients' => $administrators->pluck('name')->toArray(),
            'old_hash'                => $oldHash,
            'new_hash'                => $newHash,
            'hash_changed'            => $hashChanged,
        ]);
    }

    /**
     * Format hash change summary for notifications
     */
    protected function formatHashChangeSummary(Evidence $evidence, array $changes): array
    {
        $summary = [
            'evidence_id' => $evidence->id,
            'file_updated' => isset($changes['file']),
            'changes' => [],
        ];

        // Format each change
        foreach ($changes as $field => $change) {
            if ($field === 'file' && is_array($change)) {
                $summary['changes']['file'] = [
                    'old_hash' => $change['old_hash'] ?? null,
                    'new_hash' => $change['new_hash'] ?? null,
                    'old_path' => $change['old_path'] ?? null,
                    'new_path' => $change['new_path'] ?? null,
                    'hash_changed' => ($change['old_hash'] ?? null) !== ($change['new_hash'] ?? null),
                ];
            } elseif (is_array($change) && isset($change['old']) && isset($change['new'])) {
                $summary['changes'][$field] = [
                    'old' => $change['old'],
                    'new' => $change['new'],
                    'changed' => $change['old'] !== $change['new'],
                ];
            } else {
                $summary['changes'][$field] = $change;
            }
        }

        return $summary;
    }

    /**
     * Notify administrators of evidence deletion
     */
    protected function notifyAdministratorsOfEvidenceDeletion(Evidence $evidence): void
    {
        $administrators = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [
                'super-admin', 'administrator', 'system-administrator',
                'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin',
                'zrp-system-admin', 'judicial-system-admin', 'judicial-courts-admin',
            ]);
        })->get()->unique('id');

        if ($administrators->isEmpty()) {
            return;
        }

        // Prepare deletion details
        $deletionDetails = [
            'evidence_id' => $evidence->id,
            'evidence_title' => $evidence->title,
            'case_reference' => $evidence->case_reference,
            'exhibit_number' => $evidence->exhibit_number,
            'evidence_type' => $evidence->evidence_type,
            'deleted_by' => Auth::user()->name,
            'deleted_at' => now()->toISOString(),
            'file_path' => $evidence->file_path,
            'institution' => $evidence->institution->name ?? null,
            'department' => $evidence->department->name ?? null,
        ];

        $message = Auth::user()->name . ' permanently deleted evidence "' . $evidence->title . '" (ID: ' . $evidence->id . ').';

        if ($evidence->file_path) {
            $message .= ' Associated file was also removed from storage.';
        }

        $notificationData = [
            'title' => 'Evidence Deleted: ' . $evidence->title,
            'message' => $message,
            'action_url' => route('evidence.show', $evidence), // Link to evidence show page - will show deleted status
            'action_text' => 'View Deleted Evidence',
            'details' => $deletionDetails,
            'hash_summary' => null, // No hash summary for deletions
        ];

        foreach ($administrators as $admin) {
            $admin->notify(new GeneralNotification($notificationData));
        }

        // Also log this security event
        Auth::user()->logActivity('evidence_deletion_notification_sent', 'warning', [
            'evidence_id' => $evidence->id,
            'notification_recipients' => $administrators->pluck('name')->toArray(),
            'deletion_details' => $deletionDetails,
        ]);
    }

    /**
     * Notify administrators of evidence creation
     */
    protected function notifyAdministratorsOfEvidenceCreation(Evidence $evidence): void
    {
        // Notify all admin roles including super-admin and all institution admins
        $allAdminRoles = [
            'super-admin', 'administrator', 'system-administrator',
            'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin',
            'zrp-system-admin', 'judicial-system-admin', 'judicial-courts-admin',
        ];

        $administrators = User::whereHas('roles', function ($query) use ($allAdminRoles) {
            $query->whereIn('name', $allAdminRoles);
        })->get()->unique('id');

        if ($administrators->isEmpty()) {
            return;
        }

        // Load cross-institution instructions from settings
        $settingsService = app(\App\Services\SettingsService::class);
        $crossNotify = $settingsService->get('cross_institution_notify', true);
        $instructions = $settingsService->get('evidence_instructions', '');

        $uploadingInstitution = $evidence->institution->name ?? 'Unknown Institution';

        $creationDetails = [
            'evidence_id'    => $evidence->id,
            'evidence_title' => $evidence->title,
            'case_reference' => $evidence->case_reference,
            'exhibit_number' => $evidence->exhibit_number,
            'evidence_type'  => $evidence->evidence_type,
            'collected_date' => $evidence->collected_date,
            'created_by'     => Auth::user()->name,
            'created_at'     => now()->toISOString(),
            'file_hash'      => $evidence->file_hash,
            'institution'    => $uploadingInstitution,
            'department'     => $evidence->department->name ?? null,
            'status'         => $evidence->status,
        ];

        $baseMessage = Auth::user()->name . ' of ' . $uploadingInstitution
            . ' registered new evidence: "' . $evidence->title . '"'
            . ' (Case Ref: ' . ($evidence->case_reference ?? 'N/A')
            . ', Exhibit: ' . ($evidence->exhibit_number ?? 'N/A') . ').';

        if ($evidence->file_hash) {
            $baseMessage .= ' Integrity hash: ' . substr($evidence->file_hash, 0, 20) . '...';
        }

        foreach ($administrators as $admin) {
            // Personalise message for admins from other institutions
            $adminInstitution = $admin->institution?->name ?? null;
            $isSameInstitution = $adminInstitution === $uploadingInstitution;

            $message = $baseMessage;

            // Add cross-institution instructions only when enabled and admin is from another org
            if ($crossNotify && !empty($instructions) && !empty($adminInstitution) && !$isSameInstitution) {
                $message .= "

--- Instructions for {$adminInstitution} ---
" . $instructions;
            }

            $admin->notify(new GeneralNotification([
                'title'       => 'New Evidence Uploaded: ' . $evidence->title,
                'message'     => $message,
                'action_url'  => route('evidence.show', $evidence),
                'action_text' => 'Review Evidence',
                'details'     => $creationDetails,
                'type'        => $isSameInstitution ? 'evidence_created' : 'cross_institution_evidence',
                'hash_summary' => null,
            ]));
        }

        Auth::user()->logActivity('evidence_creation_notification_sent', 'info', [
            'evidence_id'             => $evidence->id,
            'notification_recipients' => $administrators->pluck('name')->toArray(),
            'cross_institution_notify' => $crossNotify,
        ]);
    }

    /**     * Notify administrators of evidence verification/rejection
     */
    protected function notifyAdministratorsOfEvidenceVerification(Evidence $evidence, string $action, ?string $notes): void
    {
        $administrators = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [
                'super-admin', 'administrator', 'system-administrator',
                'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin',
                'zrp-system-admin', 'judicial-system-admin', 'judicial-courts-admin',
            ]);
        })->get()->unique('id');

        if ($administrators->isEmpty()) {
            return;
        }

        // Prepare verification details
        $verificationDetails = [
            'evidence_id' => $evidence->id,
            'evidence_title' => $evidence->title,
            'case_reference' => $evidence->case_reference,
            'exhibit_number' => $evidence->exhibit_number,
            'action' => $action, // 'approve' or 'reject'
            'verified_by' => Auth::user()->name,
            'verified_at' => now()->toISOString(),
            'verification_notes' => $notes,
            'previous_status' => $evidence->getOriginal('status'),
            'new_status' => $evidence->status,
            'collected_by' => $evidence->collectedBy->name ?? null,
        ];

        if ($action === 'approve') {
            $title = 'Evidence Verified: ' . $evidence->title;
            $message = Auth::user()->name . ' verified evidence "' . $evidence->title . '" (ID: ' . $evidence->id . '). Evidence is now officially verified.';
        } else {
            $title = 'Evidence Rejected: ' . $evidence->title;
            $message = Auth::user()->name . ' rejected evidence "' . $evidence->title . '" (ID: ' . $evidence->id . ').';
            if ($notes) {
                $message .= ' Reason: ' . $notes;
            }
        }

        $notificationData = [
            'title' => $title,
            'message' => $message,
            'action_url' => route('evidence.show', $evidence),
            'action_text' => 'Review Decision',
            'details' => $verificationDetails,
            'hash_summary' => null, // No hash summary for verification
        ];

        foreach ($administrators as $admin) {
            $admin->notify(new GeneralNotification($notificationData));
        }

        // Also log this security event
        Auth::user()->logActivity('evidence_verification_notification_sent', 'info', [
            'evidence_id' => $evidence->id,
            'notification_recipients' => $administrators->pluck('name')->toArray(),
            'verification_details' => $verificationDetails,
        ]);
    }

    /**
     * Notify administrators of evidence archiving
     */
    protected function notifyAdministratorsOfEvidenceArchiving(Evidence $evidence): void
    {
        $administrators = User::whereHas('roles', function ($query) {
            $query->whereIn('name', [
                'super-admin', 'administrator', 'system-administrator',
                'rbz-system-admin', 'zacc-system-admin', 'npa-system-admin',
                'zrp-system-admin', 'judicial-system-admin', 'judicial-courts-admin',
            ]);
        })->get()->unique('id');

        if ($administrators->isEmpty()) {
            return;
        }

        // Prepare archiving details
        $archivingDetails = [
            'evidence_id' => $evidence->id,
            'evidence_title' => $evidence->title,
            'case_reference' => $evidence->case_reference,
            'exhibit_number' => $evidence->exhibit_number,
            'archived_by' => Auth::user()->name,
            'archived_at' => now()->toISOString(),
            'previous_status' => $evidence->getOriginal('status'),
            'new_status' => Evidence::STATUS_ARCHIVED,
            'collected_by' => $evidence->collectedBy->name ?? null,
        ];

        $message = Auth::user()->name . ' archived evidence "' . $evidence->title . '" (ID: ' . $evidence->id . '). Evidence is now in archived status.';

        $notificationData = [
            'title' => 'Evidence Archived: ' . $evidence->title,
            'message' => $message,
            'action_url' => route('evidence.show', $evidence),
            'action_text' => 'View Archived Evidence',
            'details' => $archivingDetails,
            'hash_summary' => null, // No hash summary for archiving
        ];

        foreach ($administrators as $admin) {
            $admin->notify(new GeneralNotification($notificationData));
        }

        // Also log this security event
        Auth::user()->logActivity('evidence_archiving_notification_sent', 'info', [
            'evidence_id' => $evidence->id,
            'notification_recipients' => $administrators->pluck('name')->toArray(),
            'archiving_details' => $archivingDetails,
        ]);
    }

    /**
     * Verify the evidence.
     */
    public function verify(Request $request, Evidence $evidence)
    {
        // Check authorization
        if (!Auth::user()->can('verify-evidence')) {
            return redirect()->route('evidence.show', $evidence)
                ->with('error', 'You are not authorized to verify evidence.');
        }

        if ($evidence->collected_by_user_id === Auth::id()) {
            return redirect()->route('evidence.show', $evidence)
                ->with('error', 'You may not verify evidence that you collected.');
        }

        $validated = $request->validate([
            'verification_notes' => 'nullable|string',
            'action' => ['required', Rule::in(['approve', 'reject'])],
        ]);

        if ($validated['action'] === 'approve') {
            $evidence->markAsVerified(Auth::id(), $validated['verification_notes']);
            $message = 'Evidence successfully verified.';
            $action = 'evidence_verified';
        } else {
            $evidence->reject(Auth::id(), $validated['verification_notes']);
            $message = 'Evidence has been rejected.';
            $action = 'evidence_rejected';
        }

        Auth::user()->logActivity($action, 'success', [
            'evidence_id' => $evidence->id,
            'verification_action' => $validated['action'],
        ]);

        // Send notification for verification/rejection
        $this->notifyAdministratorsOfEvidenceVerification($evidence, $validated['action'], $validated['verification_notes']);

        return redirect()->route('evidence.show', $evidence)
            ->with('success', $message);
    }

    /**
     * Archive the evidence.
     */
    public function archive(Evidence $evidence)
    {
        // Check authorization
        if (!Auth::user()->hasAnyRole('administrator', 'system-administrator')) {
            return redirect()->route('evidence.show', $evidence)
                ->with('error', 'You are not authorized to archive evidence.');
        }

        if ($evidence->status === Evidence::STATUS_ARCHIVED) {
            return redirect()->route('evidence.show', $evidence)
                ->with('warning', 'Evidence is already archived.');
        }

        $evidence->update(['status' => Evidence::STATUS_ARCHIVED]);

        Auth::user()->logActivity('evidence_archived', 'success', [
            'evidence_id' => $evidence->id,
        ]);

        // Send notification for archiving
        $this->notifyAdministratorsOfEvidenceArchiving($evidence);

        return redirect()->route('evidence.show', $evidence)
            ->with('success', 'Evidence successfully archived.');
    }

    /**
     * Delete the evidence.
     */
    public function destroy(Evidence $evidence)
    {
        // Check authorization
        if (!Auth::user()->hasAnyRole('system-administrator')) {
            return redirect()->route('evidence.index')
                ->with('error', 'Only system administrators can delete evidence.');
        }

        // Send notification before deletion
        $this->notifyAdministratorsOfEvidenceDeletion($evidence);

        // Delete file if exists
        if ($evidence->file_path) {
            Storage::disk('evidence')->delete($evidence->file_path);
        }

        Auth::user()->logActivity('evidence_deleted', 'success', [
            'evidence_id' => $evidence->id,
        ]);

        // Delete evidence
        $evidence->delete();

        return redirect()->route('evidence.index')
            ->with('success', 'Evidence successfully deleted.');
    }

    /**
     * Download evidence file
     */
    public function download(Evidence $evidence)
    {
        if (!$evidence->file_path || !Storage::disk('evidence')->exists($evidence->file_path)) {
            Auth::user()->logActivity('evidence_download_failed', 'warning', [
                'evidence_id' => $evidence->id,
                'reason' => 'no_file',
            ]);

            return redirect()->back()
                ->with('error', 'No file associated with this evidence.');
        }

        if (! $evidence->canBeDownloadedBy(Auth::user())) {
            Auth::user()->logActivity('evidence_download_denied', 'warning', [
                'evidence_id' => $evidence->id,
            ]);

            return redirect()->route('evidence.show', $evidence)
                ->with('error', 'You are allowed to view this evidence here, but you are not authorized to download the file.');
        }

        try {
            $encryptedFile = Storage::disk('evidence')->get($evidence->file_path);
            $fileContents = decrypt($encryptedFile);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $exception) {
            Auth::user()->logActivity('evidence_tamper_detected', 'danger', [
                'evidence_id' => $evidence->id,
                'reason' => 'decryption_failed',
            ]);

            return redirect()->route('evidence.show', $evidence)
                ->with('error', 'Evidence file integrity could not be verified. Contact an administrator.');
        }

        $currentHash = hash('sha256', $fileContents);

        if ($currentHash !== $evidence->file_hash) {
            Auth::user()->logActivity('evidence_tamper_detected', 'danger', [
                'evidence_id' => $evidence->id,
                'file_hash' => $currentHash,
                'expected_hash' => $evidence->file_hash,
            ]);

            return redirect()->route('evidence.show', $evidence)
                ->with('error', 'Evidence file integrity check failed. Contact an administrator.');
        }

        Auth::user()->logActivity('evidence_downloaded', 'success', [
            'evidence_id' => $evidence->id,
            'file_hash' => $currentHash,
            'integrity_verified' => true,
        ]);

        return response()->streamDownload(function () use ($fileContents) {
            echo $fileContents;
        }, basename($evidence->file_path), [
            'Content-Type' => $evidence->file_type ?? 'application/octet-stream',
            'Content-Length' => strlen($fileContents),
        ]);
    }

    /**
     * View evidence file inline in the browser.
     */
    public function view(Evidence $evidence)
    {
        if (!$evidence->file_path || !Storage::disk('evidence')->exists($evidence->file_path)) {
            Auth::user()->logActivity('evidence_view_failed', 'warning', [
                'evidence_id' => $evidence->id,
                'reason' => 'no_file',
            ]);

            return redirect()->back()
                ->with('error', 'No file associated with this evidence.');
        }

        if (! $evidence->canBeViewedBy(Auth::user())) {
            Auth::user()->logActivity('evidence_view_denied', 'warning', [
                'evidence_id' => $evidence->id,
            ]);

            return redirect()->route('evidence.show', $evidence)
                ->with('error', 'You are not authorized to view this evidence file.');
        }

        if ($evidence->file_type !== 'application/pdf') {
            return redirect()->route('evidence.show', $evidence)
                ->with('warning', 'File preview is only available for PDF evidence.');
        }

        return view('evidence.preview', compact('evidence'));
    }

    /**
     * Provide decrypted PDF bytes for secure preview.
     */
    public function previewFile(Evidence $evidence)
    {
        if (!$evidence->file_path || !Storage::disk('evidence')->exists($evidence->file_path)) {
            Auth::user()->logActivity('evidence_view_failed', 'warning', [
                'evidence_id' => $evidence->id,
                'reason' => 'no_file',
            ]);

            return response()->json(['error' => 'No file associated with this evidence.'], 404);
        }

        if (! $evidence->canBeViewedBy(Auth::user())) {
            Auth::user()->logActivity('evidence_view_denied', 'warning', [
                'evidence_id' => $evidence->id,
            ]);

            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        if ($evidence->file_type !== 'application/pdf') {
            return response()->json(['error' => 'Only PDF preview is supported.'], 415);
        }

        try {
            $encryptedFile = Storage::disk('evidence')->get($evidence->file_path);
            $fileContents = decrypt($encryptedFile);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $exception) {
            Auth::user()->logActivity('evidence_tamper_detected', 'danger', [
                'evidence_id' => $evidence->id,
                'reason' => 'decryption_failed',
            ]);

            return response()->json(['error' => 'Evidence file integrity could not be verified.'], 500);
        }

        return response($fileContents, 200, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Store a file as encrypted evidence content on the private evidence disk.
     */
    protected function storeEncryptedEvidenceFile($file)
    {
        $filename = uniqid('', true) . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
        $filePath = 'evidence/' . $filename;
        $fileContents = file_get_contents($file->getRealPath());
        Storage::disk('evidence')->put($filePath, encrypt($fileContents));

        return $filePath;
    }

    /**
     * Create a hash history entry for evidence integrity tracking
     */
    protected function createHashHistoryEntry(Evidence $evidence, string $changeType, User $user, array $options = [])
    {
        $contentHash = null;
        $metadataHash = null;

        // Generate content hash — always includes ALL key metadata so any
        // field change (title, case ref, description, etc.) produces a new hash.
        $fileHash = null;
        if ($evidence->file_path) {
            $filePath = storage_path('app/evidence/' . $evidence->file_path);
            if (file_exists($filePath)) {
                $fileHash = hash_file('sha256', $filePath);
            }
        }

        $contentData = [
            'file_hash'            => $fileHash ?? $evidence->file_hash,
            'title'                => $evidence->title,
            'case_reference'       => $evidence->case_reference,
            'exhibit_number'       => $evidence->exhibit_number,
            'evidence_type'        => $evidence->evidence_type,
            'description'          => $evidence->description,
            'collected_date'       => $evidence->collected_date?->toISOString(),
            'source'               => $evidence->source,
            'location_found'       => $evidence->location_found,
            'classification_level' => $evidence->classification_level,
            'institution_id'       => $evidence->institution_id,
            'department_id'        => $evidence->department_id,
        ];
        $contentHash = hash('sha256', json_encode($contentData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        // Hash metadata
        if ($evidence->metadata) {
            $metadataHash = hash('sha256', json_encode($evidence->metadata));
        }

        // Get previous state
        $previousEntry = \App\Models\EvidenceHashHistory::where('evidence_id', $evidence->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $previousState = null;
        if ($previousEntry) {
            $previousState = [
                'content_hash' => $previousEntry->content_hash,
                'metadata_hash' => $previousEntry->metadata_hash,
                'created_at' => $previousEntry->created_at,
            ];
        }

        return \App\Models\EvidenceHashHistory::create([
            'evidence_id' => $evidence->id,
            'hash_type' => 'sha256',
            'content_hash' => $contentHash,
            'metadata_hash' => $metadataHash,
            'change_type' => $changeType,
            'previous_state' => $previousState,
            'changed_fields' => $options['changed_fields'] ?? null,
            'user_id' => $user->id,
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'action' => $options['action'] ?? null,
            'notes' => $options['notes'] ?? null,
        ]);
    }
}
