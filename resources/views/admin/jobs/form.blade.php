@extends('layouts.admin')
@section('title', $job->exists ? 'Edit Opening' : 'Add Opening')

@php
    $inp = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none';
    $lbl = 'mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500';
@endphp

@section('content')
    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">Please fix the highlighted fields and try again.</div>
    @endif

    <form method="POST" action="{{ $job->exists ? route('admin.jobs.update', $job) : route('admin.jobs.store') }}" class="space-y-6">
        @csrf
        @if ($job->exists) @method('PUT') @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-5">
            <div>
                <label class="{{ $lbl }}">Job Title *</label>
                <input name="title" value="{{ old('title', $job->title) }}" required class="{{ $inp }}" placeholder="Senior Laravel Developer">
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="{{ $lbl }}">Slug</label>
                    <input name="slug" value="{{ old('slug', $job->slug) }}" class="{{ $inp }}" placeholder="auto-generated from title">
                    @error('slug')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="{{ $lbl }}">Department</label>
                    <input name="department" value="{{ old('department', $job->department) }}" class="{{ $inp }}" placeholder="Engineering">
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="{{ $lbl }}">Employment Type *</label>
                    <select name="employment_type" class="{{ $inp }}">
                        @foreach (\App\Models\JobOpening::TYPES as $val => $label)
                            <option value="{{ $val }}" @selected(old('employment_type', $job->employment_type) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $lbl }}">Experience Level</label>
                    <input name="experience_level" value="{{ old('experience_level', $job->experience_level) }}" class="{{ $inp }}" placeholder="Senior">
                </div>
                <div>
                    <label class="{{ $lbl }}">Location</label>
                    <input name="location" value="{{ old('location', $job->location) }}" class="{{ $inp }}" placeholder="Remote — Worldwide">
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="{{ $lbl }}">Salary Range (optional)</label>
                    <input name="salary_range" value="{{ old('salary_range', $job->salary_range) }}" class="{{ $inp }}" placeholder="Competitive / $X–$Y">
                </div>
                <div>
                    <label class="{{ $lbl }}">External Apply URL (optional)</label>
                    <input name="apply_url" value="{{ old('apply_url', $job->apply_url) }}" class="{{ $inp }}" placeholder="Leave blank to use the contact form">
                    @error('apply_url')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-5">
            <div>
                <label class="{{ $lbl }}">Short Summary</label>
                <textarea name="summary" rows="2" class="{{ $inp }}" placeholder="One or two sentences shown on the careers list.">{{ old('summary', $job->summary) }}</textarea>
            </div>
            <div>
                <label class="{{ $lbl }}">Role Description</label>
                <textarea name="description" rows="4" class="{{ $inp }}">{{ old('description', $job->description) }}</textarea>
            </div>
            <div>
                <label class="{{ $lbl }}">Responsibilities <span class="font-normal normal-case text-slate-400">(one per line)</span></label>
                <textarea name="responsibilities" rows="5" class="{{ $inp }}">{{ old('responsibilities', $job->responsibilities) }}</textarea>
            </div>
            <div>
                <label class="{{ $lbl }}">Requirements <span class="font-normal normal-case text-slate-400">(one per line)</span></label>
                <textarea name="requirements" rows="5" class="{{ $inp }}">{{ old('requirements', $job->requirements) }}</textarea>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-5">
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="{{ $lbl }}">Posted Date</label>
                    <input type="date" name="posted_at" value="{{ old('posted_at', optional($job->posted_at)->format('Y-m-d')) }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="{{ $lbl }}">Closes On (optional)</label>
                    <input type="date" name="closes_at" value="{{ old('closes_at', optional($job->closes_at)->format('Y-m-d')) }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="{{ $lbl }}">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $job->sort_order ?? 0) }}" class="{{ $inp }}">
                </div>
            </div>
            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="hidden" name="is_remote" value="0">
                    <input type="checkbox" name="is_remote" value="1" @checked(old('is_remote', $job->is_remote)) class="rounded border-slate-300"> Remote role
                </label>
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $job->is_published)) class="rounded border-slate-300"> Published (visible on the site)
                </label>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-slate-700">{{ $job->exists ? 'Save Changes' : 'Create Opening' }}</button>
            <a href="{{ route('admin.jobs.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Cancel</a>
        </div>
    </form>
@endsection
