<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    private array $groupLabels = [
        'general' => 'General', 'contact' => 'Contact', 'social' => 'Social Media',
        'seo' => 'Default SEO', 'analytics' => 'Analytics & Tracking', 'mail' => 'Email',
    ];

    public function edit(): View
    {
        return view('admin.settings.edit', [
            'groups' => Setting::orderBy('id')->get()->groupBy('group'),
            'groupLabels' => $this->groupLabels,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $all = Setting::all();

        foreach ($all as $setting) {
            if ($setting->type === 'boolean') {
                $setting->value = $request->boolean("settings.{$setting->key}") ? '1' : '0';
                $setting->save();

                continue;
            }

            if ($setting->type === 'image' && $request->hasFile("files.{$setting->key}")) {
                $request->validate(["files.{$setting->key}" => ['mimes:jpeg,jpg,png,gif,webp,ico', 'max:2048']]);
                $path = $request->file("files.{$setting->key}")->store('settings', 'public');
                $setting->value = Storage::disk('public')->url($path);
                $setting->save();

                continue;
            }

            if ($request->has("settings.{$setting->key}")) {
                $setting->value = $request->input("settings.{$setting->key}");
                $setting->save();
            }
        }

        Cache::forget(Setting::CACHE_KEY);
        ActivityLog::record('settings.updated', 'Site settings updated');

        return back()->with('status', 'Settings saved successfully.');
    }
}
