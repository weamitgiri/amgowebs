<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Page;
use App\Models\PageSection;
use App\Support\HtmlSanitizer;
use App\Support\SectionTypes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.index', [
            'pages' => Page::withCount('sections')->orderBy('title')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.form', ['page' => new Page(['status' => 'draft', 'is_in_sitemap' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $page = Page::create($this->validateMeta($request) + ['author_id' => auth()->id()]);
        $this->syncSeo($page, $request);
        ActivityLog::record('page.created', "Page “{$page->title}” created", $page);

        return redirect()->route('admin.pages.edit', $page)->with('status', 'Page created. Now add sections.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.builder', [
            'page' => $page->load('sections'),
            'sectionTypes' => SectionTypes::all(),
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $page->update($this->validateMeta($request, $page));
        $this->syncSeo($page, $request);
        ActivityLog::record('page.updated', "Page “{$page->title}” updated", $page);

        return back()->with('status', 'Page saved.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        if ($page->is_system) {
            return back()->withErrors(['page' => 'System pages cannot be deleted.']);
        }
        $title = $page->title;
        $page->delete();
        ActivityLog::record('page.deleted', "Page “{$title}” deleted");

        return redirect()->route('admin.pages.index')->with('status', 'Page deleted.');
    }

    // ---- Sections ----

    public function addSection(Request $request, Page $page): RedirectResponse
    {
        $type = $request->input('type');
        abort_unless(SectionTypes::exists($type), 422);

        // Custom HTML is restricted to super admins.
        if (! empty(SectionTypes::all()[$type]['restricted']) && ! $request->user()->isSuperAdmin()) {
            abort(403, 'Only super admins can add custom HTML sections.');
        }

        $page->sections()->create([
            'type' => $type,
            'data' => SectionTypes::defaults($type),
            'sort_order' => (int) $page->sections()->max('sort_order') + 1,
        ]);

        return back()->with('status', 'Section added.');
    }

    public function updateSection(Request $request, Page $page, PageSection $section): RedirectResponse
    {
        abort_unless($section->page_id === $page->id, 404);

        $section->update([
            'data' => $this->cleanData($section->type, $request->input('data', [])),
            'is_visible' => $request->boolean('is_visible'),
        ]);

        return back()->with('status', 'Section updated.');
    }

    public function duplicateSection(Page $page, PageSection $section): RedirectResponse
    {
        abort_unless($section->page_id === $page->id, 404);

        $page->sections()->create([
            'type' => $section->type,
            'data' => $section->data,
            'is_visible' => $section->is_visible,
            'sort_order' => (int) $page->sections()->max('sort_order') + 1,
        ]);

        return back()->with('status', 'Section duplicated.');
    }

    public function deleteSection(Page $page, PageSection $section): RedirectResponse
    {
        abort_unless($section->page_id === $page->id, 404);
        $section->delete();

        return back()->with('status', 'Section removed.');
    }

    public function reorderSections(Request $request, Page $page): RedirectResponse
    {
        foreach ((array) $request->input('order', []) as $position => $sectionId) {
            $page->sections()->whereKey($sectionId)->update(['sort_order' => $position]);
        }

        return back()->with('status', 'Section order saved.');
    }

    // ---- Helpers ----

    private function validateMeta(Request $request, ?Page $page = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('pages', 'slug')->ignore($page?->id)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'is_in_sitemap' => ['nullable'],
        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['is_in_sitemap'] = $request->boolean('is_in_sitemap');
        $data['published_at'] = $data['status'] === 'published' ? ($page?->published_at ?? now()) : null;

        return $data;
    }

    private function cleanData(string $type, array $data): array
    {
        $isRestricted = ! empty(SectionTypes::all()[$type]['restricted']);

        foreach (SectionTypes::fields($type) as $field) {
            // Strip tags from non-HTML text fields to prevent stored XSS.
            if (in_array($field['type'], ['text', 'textarea', 'url'], true) && isset($data[$field['key']]) && is_string($data[$field['key']])) {
                $data[$field['key']] = strip_tags($data[$field['key']]);
            }
            // Sanitize HTML fields, except restricted (super-admin-only) custom HTML.
            if ($field['type'] === 'html' && ! $isRestricted && isset($data[$field['key']]) && is_string($data[$field['key']])) {
                $data[$field['key']] = HtmlSanitizer::clean($data[$field['key']]);
            }
            if ($field['type'] === 'repeater' && isset($data[$field['key']]) && is_array($data[$field['key']])) {
                $data[$field['key']] = array_values(array_filter($data[$field['key']], fn ($row) => is_array($row) && count(array_filter($row)) > 0));
                foreach ($data[$field['key']] as &$row) {
                    foreach (($field['fields'] ?? []) as $sub) {
                        if (isset($row[$sub['key']]) && is_string($row[$sub['key']])) {
                            $row[$sub['key']] = strip_tags($row[$sub['key']]);
                        }
                    }
                }
            }
        }

        return $data;
    }

    private function syncSeo(Page $page, Request $request): void
    {
        if ($request->filled('seo_title') || $request->filled('seo_description') || $request->filled('robots')) {
            $page->seo()->updateOrCreate([], [
                'title' => $request->input('seo_title'),
                'description' => $request->input('seo_description'),
                'robots' => $request->input('robots', 'index, follow'),
            ]);
        }
    }
}
