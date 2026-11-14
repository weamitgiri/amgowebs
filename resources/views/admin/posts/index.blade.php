@extends('layouts.admin')
@section('title', 'Blog')

@section('content')
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm text-slate-500">Manage blog posts, categories and tags.</p>
        @permission('blog.create')
            <a href="{{ route('admin.posts.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">+ New Post</a>
        @endpermission
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-4 py-3">Title</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Date</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($posts as $post)
                    @php
                        $scheduled = $post->status === 'published' && $post->published_at && $post->published_at->isFuture();
                        [$badgeText, $badgeClass] = $scheduled
                            ? ['Scheduled', 'bg-amber-100 text-amber-700']
                            : ($post->status === 'published' ? ['Published', 'bg-green-100 text-green-700'] : ['Draft', 'bg-slate-200 text-slate-600']);
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><p class="font-medium text-slate-900">{{ $post->title }}</p><p class="text-xs text-slate-500">/{{ $post->slug }} · by {{ $post->author?->name ?? '—' }}</p></td>
                        <td class="px-4 py-3 text-slate-600">{{ $post->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $badgeClass }}">{{ $badgeText }}</span></td>
                        <td class="px-4 py-3 text-slate-500">{{ $post->published_at?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-slate-400 hover:text-slate-700">View</a>
                                @permission('blog.edit')<a href="{{ route('admin.posts.edit', $post) }}" class="text-cyan-600 hover:underline">Edit</a>@endpermission
                                @permission('blog.delete')
                                    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline">Delete</button></form>
                                @endpermission
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-slate-400">No posts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $posts->links() }}</div>
@endsection
