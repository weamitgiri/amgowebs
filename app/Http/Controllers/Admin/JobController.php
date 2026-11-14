<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Models\ActivityLog;
use App\Models\JobOpening;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(): View
    {
        return view('admin.jobs.index', ['jobs' => JobOpening::ordered()->get()]);
    }

    public function create(): View
    {
        return view('admin.jobs.form', ['job' => new JobOpening([
            'employment_type' => 'full-time',
            'is_remote' => true,
            'is_published' => true,
            'location' => 'Remote — Worldwide',
            'posted_at' => now(),
        ])]);
    }

    public function store(JobRequest $request): RedirectResponse
    {
        $job = JobOpening::create($request->validated());
        ActivityLog::record('job.created', "Job “{$job->title}” created", $job);

        return redirect()->route('admin.jobs.index')->with('status', 'Job opening created.');
    }

    public function edit(JobOpening $job): View
    {
        return view('admin.jobs.form', ['job' => $job]);
    }

    public function update(JobRequest $request, JobOpening $job): RedirectResponse
    {
        $job->update($request->validated());
        ActivityLog::record('job.updated', "Job “{$job->title}” updated", $job);

        return redirect()->route('admin.jobs.index')->with('status', 'Job opening updated.');
    }

    public function toggle(JobOpening $job): RedirectResponse
    {
        $job->update(['is_published' => ! $job->is_published]);
        ActivityLog::record('job.toggled', "Job “{$job->title}” visibility toggled", $job);

        return back()->with('status', 'Job updated.');
    }

    public function destroy(JobOpening $job): RedirectResponse
    {
        $title = $job->title;
        $job->delete();
        ActivityLog::record('job.deleted', "Job “{$title}” deleted");

        return redirect()->route('admin.jobs.index')->with('status', 'Job opening deleted.');
    }
}
