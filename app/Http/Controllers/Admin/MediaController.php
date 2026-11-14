<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(): View
    {
        return view('admin.media.index', [
            'media' => Media::latest()->paginate(24),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpeg,jpg,png,gif,webp', 'max:8192'],
            'alt' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads', 'public');
        $dimensions = @getimagesize($file->getRealPath()) ?: [null, null];

        Media::create([
            'disk' => 'public',
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'width' => $dimensions[0] ?? null,
            'height' => $dimensions[1] ?? null,
            'alt' => $request->input('alt'),
            'folder' => 'uploads',
            'uploaded_by' => auth()->id(),
        ]);

        ActivityLog::record('media.uploaded', "Uploaded {$file->getClientOriginalName()}");

        return back()->with('status', 'File uploaded.');
    }

    public function update(Request $request, Media $medium): RedirectResponse
    {
        $medium->update($request->validate([
            'alt' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
        ]));

        return back()->with('status', 'Media updated.');
    }

    public function destroy(Media $medium): RedirectResponse
    {
        Storage::disk($medium->disk)->delete($medium->path);
        $medium->delete();
        ActivityLog::record('media.deleted', "Deleted {$medium->filename}");

        return back()->with('status', 'File deleted.');
    }
}
