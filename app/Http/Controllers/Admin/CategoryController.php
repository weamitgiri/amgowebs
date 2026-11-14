<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::ordered()->with('parent')->withCount(['posts', 'children'])->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.form', [
            'category' => new Category(['is_active' => true]),
            'parents' => Category::topLevel()->ordered()->get(),
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $category = Category::create($this->data($request));
        $this->syncSeo($category, $request);
        ActivityLog::record('category.created', "Category “{$category->name}” created", $category);

        return redirect()->route('admin.categories.index')->with('status', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', [
            'category' => $category,
            'parents' => Category::topLevel()->where('id', '!=', $category->id)->ordered()->get(),
        ]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($this->data($request));
        $this->syncSeo($category, $request);
        ActivityLog::record('category.updated', "Category “{$category->name}” updated", $category);

        return redirect()->route('admin.categories.index')->with('status', 'Category updated.');
    }

    public function toggle(Category $category): RedirectResponse
    {
        $category->update(['is_active' => ! $category->is_active]);
        ActivityLog::record('category.toggled', "Category “{$category->name}” visibility toggled", $category);

        return back()->with('status', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $name = $category->name;
        // Safe delete: children become top-level; posts are set to null by the FK constraint.
        $category->children()->update(['parent_id' => null]);
        $category->delete();
        ActivityLog::record('category.deleted', "Category “{$name}” deleted");

        return redirect()->route('admin.categories.index')->with('status', 'Category deleted — its posts are now uncategorised.');
    }

    /** @return array<string, mixed> */
    private function data(CategoryRequest $request): array
    {
        $data = $request->safe()->except(['seo_title', 'seo_description', 'image']);

        if ($request->hasFile('image')) {
            $data['image'] = Storage::disk('public')->url(
                $request->file('image')->store('categories/'.date('Y/m'), 'public')
            );
        }

        return $data;
    }

    private function syncSeo(Category $category, CategoryRequest $request): void
    {
        if ($request->filled('seo_title') || $request->filled('seo_description')) {
            $category->seo()->updateOrCreate([], [
                'title' => $request->input('seo_title'),
                'description' => $request->input('seo_description'),
            ]);
        }
    }
}
