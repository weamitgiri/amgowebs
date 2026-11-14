@extends('layouts.admin')
@section('title', 'Inquiry ' . $inquiry->reference)

@section('content')
    <a href="{{ route('admin.inquiries.index') }}" class="mb-4 inline-block text-sm text-slate-500 hover:text-slate-800">← Back to inquiries</a>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Main details --}}
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">{{ $inquiry->name }}</h2>
                        <p class="text-sm text-slate-500">{{ $inquiry->reference }} · submitted {{ $inquiry->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    @permission('inquiries.manage')
                        <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}" onsubmit="return confirm('Delete this inquiry permanently?')">
                            @csrf @method('DELETE')
                            <button class="text-sm text-red-600 hover:underline">Delete</button>
                        </form>
                    @endpermission
                </div>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    @foreach ([
                        'Business' => $inquiry->business_name, 'Email' => $inquiry->email, 'Phone' => $inquiry->phone,
                        'Country' => $inquiry->country, 'Package' => $inquiry->package?->name, 'Website type' => $inquiry->website_type,
                        'Pages' => $inquiry->pages_required, 'Budget' => $inquiry->budget,
                        'Existing site' => $inquiry->existing_url, 'Preferred meeting' => $inquiry->preferred_meeting_at?->format('M j, Y g:i A'),
                    ] as $k => $v)
                        <div><dt class="text-slate-400">{{ $k }}</dt><dd class="font-medium text-slate-800">{{ $v ?: '—' }}</dd></div>
                    @endforeach
                </dl>
                <div class="mt-5 border-t border-slate-100 pt-4">
                    <h3 class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-400">Project description</h3>
                    <p class="whitespace-pre-line text-sm text-slate-700">{{ $inquiry->description }}</p>
                </div>
                @if ($inquiry->required_features)
                    <div class="mt-4"><h3 class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-400">Required features</h3><p class="whitespace-pre-line text-sm text-slate-700">{{ $inquiry->required_features }}</p></div>
                @endif
                @if ($inquiry->attachment_path)
                    <div class="mt-4">
                        <h3 class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-400">Attachment</h3>
                        <a href="{{ route('admin.inquiries.attachment', $inquiry) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">⬇ {{ $inquiry->attachment_name ?: 'Download file' }}</a>
                    </div>
                @endif
            </div>

            {{-- Notes --}}
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <h3 class="mb-4 font-semibold text-slate-800">Internal Notes</h3>
                @permission('inquiries.manage')
                    <form method="POST" action="{{ route('admin.inquiries.notes', $inquiry) }}" class="mb-5">
                        @csrf
                        <textarea name="body" rows="2" required placeholder="Add a note…" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">{{ old('body') }}</textarea>
                        <div class="mt-2 flex items-center gap-3">
                            <label class="text-xs text-slate-500">Follow-up: <input type="date" name="follow_up_at" class="rounded border border-slate-300 px-2 py-1 text-xs"></label>
                            <button class="rounded-lg bg-slate-900 px-4 py-1.5 text-sm font-medium text-white hover:bg-slate-700">Add note</button>
                        </div>
                    </form>
                @endpermission
                <div class="space-y-3">
                    @forelse ($inquiry->notes as $note)
                        <div class="rounded-lg bg-slate-50 p-3">
                            <p class="text-sm text-slate-700">{{ $note->body }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $note->user?->name ?? 'System' }} · {{ $note->created_at->diffForHumans() }}@if ($note->follow_up_at) · follow-up {{ $note->follow_up_at->format('M j') }}@endif</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No notes yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar: status + assign + history --}}
        <div class="space-y-6">
            @permission('inquiries.manage')
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="mb-3 text-sm font-semibold text-slate-700">Status</h3>
                    <form method="POST" action="{{ route('admin.inquiries.status', $inquiry) }}">
                        @csrf
                        <select name="status" class="mb-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            @foreach ($statuses as $key => $label)<option value="{{ $key }}" @selected($inquiry->status === $key)>{{ $label }}</option>@endforeach
                        </select>
                        <input type="text" name="note" placeholder="Optional note" class="mb-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <button class="w-full rounded-lg bg-slate-900 py-2 text-sm font-medium text-white hover:bg-slate-700">Update status</button>
                    </form>
                </div>
            @endpermission

            @permission('inquiries.assign')
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="mb-3 text-sm font-semibold text-slate-700">Assigned to</h3>
                    <form method="POST" action="{{ route('admin.inquiries.assign', $inquiry) }}">
                        @csrf
                        <select name="assigned_to" onchange="this.form.submit()" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            <option value="">Unassigned</option>
                            @foreach ($team as $member)<option value="{{ $member->id }}" @selected($inquiry->assigned_to === $member->id)>{{ $member->name }}</option>@endforeach
                        </select>
                    </form>
                </div>
            @endpermission

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="mb-3 text-sm font-semibold text-slate-700">Status History</h3>
                <ol class="space-y-3">
                    @forelse ($inquiry->statusHistory as $h)
                        <li class="flex gap-3 text-sm">
                            <span class="mt-1 h-2 w-2 flex-none rounded-full bg-cyan-500"></span>
                            <div>
                                <p class="text-slate-700">{{ $h->from_status ? ucfirst(str_replace('_',' ',$h->from_status)).' → ' : '' }}<strong>{{ ucfirst(str_replace('_',' ',$h->to_status)) }}</strong></p>
                                <p class="text-xs text-slate-400">{{ $h->user?->name ?? 'System' }} · {{ $h->created_at->diffForHumans() }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-slate-400">No history.</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>
@endsection
