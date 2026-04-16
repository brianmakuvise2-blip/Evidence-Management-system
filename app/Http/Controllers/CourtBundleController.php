<?php

namespace App\Http\Controllers;

use App\Models\CourtBundle;
use App\Models\CourtBundleDisclosure;
use App\Models\CourtBundleItem;
use App\Models\Evidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourtBundleController extends Controller
{
    public function index(Request $request)
    {
        $query = CourtBundle::query()->with(['preparedBy', 'approvedBy']);

        if (Auth::user()->hasRole('judicial-viewer')) {
            $query->approved();
        }

        if ($request->filled('case_reference')) {
            $query->where('case_reference', 'like', "%{$request->case_reference}%");
        }

        if ($request->filled('title')) {
            $query->where('title', 'like', "%{$request->title}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bundles = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('bundles.index', compact('bundles'));
    }

    public function create()
    {
        $evidenceItems = Evidence::where('status', '!=', Evidence::STATUS_DISPOSED)
            ->with(['institution', 'collectedBy'])
            ->orderByDesc('collected_date')
            ->get();

        return view('bundles.create', compact('evidenceItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'case_reference' => 'required|string|max:255',
            'description' => 'nullable|string',
            'evidence_ids' => 'required|array|min:1',
            'evidence_ids.*' => 'required|exists:evidence,id',
        ]);

        $version = CourtBundle::where('case_reference', $validated['case_reference'])->max('version');
        $version = $version ? $version + 1 : 1;

        $bundle = DB::transaction(function () use ($validated, $version) {
            $bundle = CourtBundle::create([
                'title' => $validated['title'],
                'case_reference' => $validated['case_reference'],
                'description' => $validated['description'],
                'prepared_by_user_id' => Auth::id(),
                'status' => CourtBundle::STATUS_DRAFT,
                'version' => $version,
            ]);

            foreach ($validated['evidence_ids'] as $index => $evidenceId) {
                $evidence = Evidence::find($evidenceId);
                CourtBundleItem::create([
                    'court_bundle_id' => $bundle->id,
                    'evidence_id' => $evidence->id,
                    'exhibit_number' => $evidence->exhibit_number,
                    'description' => $evidence->description,
                    'page_reference' => $index + 1,
                    'item_order' => $index + 1,
                ]);
            }

            return $bundle;
        });

        Auth::user()->logActivity('bundle_created', 'success', [
            'bundle_id' => $bundle->id,
            'case_reference' => $bundle->case_reference,
            'version' => $bundle->version,
        ]);

        return redirect()->route('bundles.show', $bundle)
            ->with('success', 'Court bundle created successfully.');
    }

    public function show(CourtBundle $bundle)
    {
        if (! $bundle->isVisibleTo(Auth::user())) {
            abort(403);
        }

        $bundle->load(['items.evidence', 'preparedBy', 'approvedBy', 'disclosures.sharedBy', 'disclosures.sharedWith']);

        if (Auth::user()->hasRole('judicial-viewer')) {
            CourtBundleDisclosure::create([
                'court_bundle_id' => $bundle->id,
                'shared_by_user_id' => Auth::id(),
                'shared_with_user_id' => Auth::id(),
                'recipient_name' => Auth::user()->name,
                'notes' => 'Judicial viewer accessed approved bundle',
            ]);
        }

        Auth::user()->logActivity('bundle_viewed', 'info', [
            'bundle_id' => $bundle->id,
        ]);

        return view('bundles.show', compact('bundle'));
    }

    public function approve(CourtBundle $bundle)
    {
        if ($bundle->isApproved()) {
            return redirect()->route('bundles.show', $bundle)
                ->with('info', 'This bundle is already approved.');
        }

        DB::transaction(function () use ($bundle) {
            CourtBundle::where('case_reference', $bundle->case_reference)
                ->where('status', CourtBundle::STATUS_APPROVED)
                ->where('id', '!=', $bundle->id)
                ->update(['status' => CourtBundle::STATUS_SUPERSEDED]);

            $bundle->update([
                'status' => CourtBundle::STATUS_APPROVED,
                'approved_by_user_id' => Auth::id(),
                'approved_at' => now(),
            ]);
        });

        Auth::user()->logActivity('bundle_approved', 'success', [
            'bundle_id' => $bundle->id,
        ]);

        return redirect()->route('bundles.show', $bundle)
            ->with('success', 'Court bundle approved successfully.');
    }

    public function export(CourtBundle $bundle)
    {
        if (! $bundle->isVisibleTo(Auth::user())) {
            abort(403);
        }

        $content = $this->buildBundlePdf($bundle);

        Auth::user()->logActivity('bundle_exported', 'success', [
            'bundle_id' => $bundle->id,
        ]);

        CourtBundleDisclosure::create([
            'court_bundle_id' => $bundle->id,
            'shared_by_user_id' => Auth::id(),
            'shared_with_user_id' => Auth::id(),
            'recipient_name' => Auth::user()->name,
            'notes' => 'Bundle PDF exported',
        ]);

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="court-bundle-' . $bundle->id . '.pdf"',
        ]);
    }

    protected function buildBundlePdf(CourtBundle $bundle): string
    {
        $lines = [];
        $lines[] = "Court Bundle: {$bundle->title}";
        $lines[] = "Case Reference: {$bundle->case_reference}";
        $preparedBy = $bundle->preparedBy?->name ?? 'Unknown';
        $approvedBy = $bundle->approvedBy?->name ?? 'Pending';

        $lines[] = "Bundle Version: {$bundle->version}";
        $lines[] = "Status: {$bundle->status}";
        $lines[] = "Prepared By: {$preparedBy}";
        $lines[] = "Approved By: {$approvedBy}";
        $lines[] = str_repeat('-', 60);
        $lines[] = "Bundle Index";
        $lines[] = str_repeat('-', 60);

        foreach ($bundle->items as $item) {
            $lines[] = sprintf('Exhibit %s | Page %d | %s', $item->exhibit_number, $item->page_reference, $item->description ?: 'No description');
        }

        $lines[] = str_repeat('-', 60);
        $lines[] = "Generated on: " . now()->toDateTimeString();

        return $this->createPdf($lines);
    }

    protected function createPdf(array $lines): string
    {
        $body = "BT /F1 12 Tf 50 760 Td\n";

        foreach ($lines as $index => $line) {
            $body .= '(' . $this->escapePdfLine($line) . ') Tj\n';
            if ($index < count($lines) - 1) {
                $body .= 'T*\n';
            }
        }

        $body .= 'ET';
        $streamLength = strlen($body);

        $obj1 = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $obj2 = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $obj3 = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n";
        $obj4 = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        $obj5 = "5 0 obj\n<< /Length {$streamLength} >>\nstream\n{$body}\nendstream\nendobj\n";

        $pdf = "%PDF-1.4\n" . $obj1 . $obj2 . $obj3 . $obj4 . $obj5;
        $xrefOffset = strlen($pdf);

        $offsets = [
            0,
            strlen("%PDF-1.4\n"),
            strlen("%PDF-1.4\n" . $obj1),
            strlen("%PDF-1.4\n" . $obj1 . $obj2),
            strlen("%PDF-1.4\n" . $obj1 . $obj2 . $obj3),
            strlen("%PDF-1.4\n" . $obj1 . $obj2 . $obj3 . $obj4),
        ];

        $xref = "xref\n0 6\n";
        $xref .= sprintf('%010d 65535 f \n', 0);

        foreach ($offsets as $offset) {
            $xref .= sprintf('%010d 00000 n \n', $offset);
        }

        $trailer = "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

        return $pdf . $xref . $trailer;
    }

    protected function escapePdfLine(string $line): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line);
    }
}
