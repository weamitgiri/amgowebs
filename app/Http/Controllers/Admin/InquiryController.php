<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Package;
use App\Models\PackageInquiry;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse as BaseStreamedResponse;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $query = PackageInquiry::query()->with('package', 'assignee');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }
        if ($packageId = $request->query('package')) {
            $query->where('package_id', $packageId);
        }
        if ($assigned = $request->query('assigned')) {
            $assigned === 'unassigned'
                ? $query->whereNull('assigned_to')
                : $query->where('assigned_to', $assigned);
        }

        return view('admin.inquiries.index', [
            'inquiries' => $query->latest()->paginate(15)->withQueryString(),
            'statusCounts' => collect(PackageInquiry::STATUSES)->mapWithKeys(fn ($l, $k) => [
                $k => PackageInquiry::where('status', $k)->count(),
            ]),
            'statuses' => PackageInquiry::STATUSES,
            'packages' => Package::orderBy('name')->get(),
            'filters' => $request->only('status', 'q', 'package', 'assigned'),
        ]);
    }

    public function show(PackageInquiry $inquiry): View
    {
        $inquiry->load(['package', 'assignee', 'notes.user', 'statusHistory.user']);

        return view('admin.inquiries.show', [
            'inquiry' => $inquiry,
            'statuses' => PackageInquiry::STATUSES,
            'team' => User::whereNotNull('role_id')->orderBy('name')->get(),
        ]);
    }

    public function attachment(PackageInquiry $inquiry): BaseStreamedResponse
    {
        abort_unless(
            $inquiry->attachment_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($inquiry->attachment_path),
            404
        );

        return \Illuminate\Support\Facades\Storage::disk('local')->download(
            $inquiry->attachment_path,
            $inquiry->attachment_name ?: 'attachment'
        );
    }

    public function updateStatus(Request $request, PackageInquiry $inquiry): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(PackageInquiry::STATUSES))],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['status'] !== $inquiry->status) {
            $from = $inquiry->status;
            $inquiry->update(['status' => $data['status']]);
            $inquiry->statusHistory()->create([
                'user_id' => $request->user()->id,
                'from_status' => $from,
                'to_status' => $data['status'],
                'note' => $data['note'] ?? null,
            ]);
            ActivityLog::record('inquiry.status', "Inquiry {$inquiry->reference}: {$from} → {$data['status']}", $inquiry);
        }

        return back()->with('status', 'Status updated.');
    }

    public function addNote(Request $request, PackageInquiry $inquiry): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'follow_up_at' => ['nullable', 'date'],
        ]);

        $inquiry->notes()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
            'follow_up_at' => $data['follow_up_at'] ?? null,
        ]);

        if (! empty($data['follow_up_at'])) {
            $inquiry->update(['follow_up_at' => $data['follow_up_at']]);
        }

        ActivityLog::record('inquiry.note', "Note added to {$inquiry->reference}", $inquiry);

        return back()->with('status', 'Note added.');
    }

    public function assign(Request $request, PackageInquiry $inquiry): RedirectResponse
    {
        $data = $request->validate([
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $inquiry->update(['assigned_to' => $data['assigned_to'] ?? null]);
        ActivityLog::record('inquiry.assign', "Inquiry {$inquiry->reference} reassigned", $inquiry);

        return back()->with('status', 'Assignment updated.');
    }

    public function destroy(PackageInquiry $inquiry): RedirectResponse
    {
        $ref = $inquiry->reference;
        $inquiry->delete();
        ActivityLog::record('inquiry.deleted', "Inquiry {$ref} deleted");

        return redirect()->route('admin.inquiries.index')->with('status', 'Inquiry deleted.');
    }

    public function export(Request $request): BaseStreamedResponse
    {
        $query = PackageInquiry::query()->with('package');
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $filename = 'inquiries-'.now()->format('Y-m-d').'.csv';
        $columns = ['Reference', 'Date', 'Name', 'Business', 'Email', 'Phone', 'Country', 'Package', 'Website Type', 'Pages', 'Budget', 'Status', 'Description'];

        return response()->streamDownload(function () use ($query, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            $query->latest()->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $i) {
                    fputcsv($out, [
                        $i->reference, $i->created_at->format('Y-m-d H:i'), $i->name, $i->business_name,
                        $i->email, $i->phone, $i->country, $i->package?->name, $i->website_type,
                        $i->pages_required, $i->budget, $i->status_label, $i->description,
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
