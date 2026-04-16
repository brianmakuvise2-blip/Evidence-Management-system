<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use App\Models\Institution;
use App\Models\Department;
use App\Models\User;
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

        Auth::user()->logActivity('evidence_registered', 'success', [
            'evidence_id' => $evidence->id,
            'file_hash' => $fileHash,
        ]);

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

        // Update evidence
        $evidence->update($validated);

        Auth::user()->logActivity('evidence_updated', 'success', [
            'evidence_id' => $evidence->id,
            'file_updated' => $request->hasFile('file'),
            'changes' => $changedFields,
        ]);

        $this->notifyAdministratorsOfEvidenceChange($evidence, $changedFields);

        return redirect()->route('evidence.show', $evidence)
            ->with('success', 'Evidence successfully updated.');
    }

    /**
     * Notify system administrators when evidence is modified.
     */
    protected function notifyAdministratorsOfEvidenceChange(Evidence $evidence, array $changes): void
    {
        $administrators = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['administrator', 'system-administrator']);
        })
        ->where('id', '!=', Auth::id())
        ->get();

        if ($administrators->isEmpty()) {
            return;
        }

        $notificationData = [
            'title' => 'Evidence Updated: ' . $evidence->title,
            'message' => Auth::user()->name . ' modified evidence "' . $evidence->title . '".',
            'action_url' => route('evidence.show', $evidence),
            'action_text' => 'Review Evidence',
            'details' => $changes,
        ];

        foreach ($administrators as $admin) {
            $admin->notify(new GeneralNotification($notificationData));
        }
    }

    /**
     * Verify the evidence.
     */
    public function verify(Request $request, Evidence $evidence)
    {
        // Check authorization
        if (!Auth::user()->hasAnyRole('administrator', 'system-administrator')) {
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
}
