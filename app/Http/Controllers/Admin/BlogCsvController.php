<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Post;
use App\Services\BlogCsvService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse as BaseStreamedResponse;

class BlogCsvController extends Controller
{
    public function __construct(private BlogCsvService $csv) {}

    public function index(): View
    {
        return view('admin.blog-csv.index', [
            'categories' => Category::orderBy('name')->get(),
            'count' => Post::count(),
        ]);
    }

    public function template(): BaseStreamedResponse
    {
        return $this->csv->streamTemplate();
    }

    public function export(Request $request): BaseStreamedResponse
    {
        $query = Post::query();
        $name = 'blog-all';

        if (($scope = $request->query('scope')) === 'published') {
            $query->where('status', 'published')->where('published_at', '<=', now());
            $name = 'blog-published';
        } elseif ($scope === 'draft') {
            $query->where('status', 'draft');
            $name = 'blog-drafts';
        }
        if ($cat = $request->query('category')) {
            $query->where('category_id', $cat);
            $name = 'blog-category';
        }

        return $this->csv->streamExport($query->latest('id'), $name);
    }

    public function preview(Request $request): View|RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:10240']]);

        $token = Str::random(24);
        Storage::disk('local')->putFileAs('blog-imports', $request->file('file'), $token.'.csv');
        $path = 'blog-imports/'.$token.'.csv';

        $rows = $this->csv->parse(Storage::disk('local')->path($path));
        if (empty($rows)) {
            Storage::disk('local')->delete($path);

            return back()->with('error', 'That CSV has a header but no data rows.');
        }

        $analysis = $this->csv->analyze($rows);

        return view('admin.blog-csv.preview', [
            'token' => $token,
            'analysis' => $analysis,
            'summary' => [
                'create' => collect($analysis)->where('action', 'create')->count(),
                'update' => collect($analysis)->where('action', 'update')->count(),
                'error' => collect($analysis)->where('action', 'error')->count(),
            ],
        ]);
    }

    public function import(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:64'],
            'update_existing' => ['nullable', 'boolean'],
            'skip_duplicates' => ['nullable', 'boolean'],
        ]);

        $path = 'blog-imports/'.basename($data['token']).'.csv';
        abort_unless(Storage::disk('local')->exists($path), 404, 'Upload session expired — please upload the CSV again.');

        $analysis = $this->csv->analyze($this->csv->parse(Storage::disk('local')->path($path)));
        $report = $this->csv->import(
            $analysis,
            $request->boolean('update_existing'),
            $request->boolean('skip_duplicates'),
            (int) auth()->id(),
        );
        Storage::disk('local')->delete($path);

        ActivityLog::record('blog.csv_import', "Blog CSV import — {$report['created']} created, {$report['updated']} updated, {$report['skipped']} skipped, {$report['failed']} failed");

        return view('admin.blog-csv.report', ['report' => $report]);
    }
}
