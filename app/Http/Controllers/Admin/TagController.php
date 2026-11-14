<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        return view('admin.tags.index', [
            'tags' => Tag::withCount('posts')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:60']]);
        $tag = Tag::firstOrCreate(['slug' => Str::slug($data['name'])], ['name' => $data['name']]);
        ActivityLog::record('tag.created', "Tag “{$tag->name}” created", $tag);

        return back()->with('status', 'Tag added.');
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'slug' => ['required', 'string', 'max:80', Rule::unique('tags', 'slug')->ignore($tag->id)],
        ]);
        $tag->update(['name' => $data['name'], 'slug' => Str::slug($data['slug'])]);

        return back()->with('status', 'Tag updated.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $name = $tag->name;
        $tag->posts()->detach();
        $tag->delete();
        ActivityLog::record('tag.deleted', "Tag “{$name}” deleted");

        return back()->with('status', 'Tag deleted.');
    }
}
