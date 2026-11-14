<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PackageRequest;
use App\Models\ActivityLog;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(): View
    {
        return view('admin.packages.index', [
            'packages' => Package::ordered()->withCount('features')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.packages.form', [
            'package' => new Package(['status' => 'published', 'is_active' => true, 'cta_label' => 'Get Started']),
        ]);
    }

    public function store(PackageRequest $request): RedirectResponse
    {
        $package = Package::create($this->packageData($request));
        $this->syncFeatures($package, $request);
        $this->syncSeo($package, $request);
        ActivityLog::record('package.created', "Package “{$package->name}” created", $package);

        return redirect()->route('admin.packages.index')->with('status', 'Package created.');
    }

    public function edit(Package $package): View
    {
        $package->load('features');

        return view('admin.packages.form', ['package' => $package]);
    }

    public function update(PackageRequest $request, Package $package): RedirectResponse
    {
        $package->update($this->packageData($request));
        $this->syncFeatures($package, $request);
        $this->syncSeo($package, $request);
        ActivityLog::record('package.updated', "Package “{$package->name}” updated", $package);

        return redirect()->route('admin.packages.index')->with('status', 'Package updated.');
    }

    public function toggle(Request $request, Package $package): RedirectResponse
    {
        $field = $request->input('field');
        abort_unless(in_array($field, ['is_active', 'is_featured'], true), 400);

        $package->update([$field => ! $package->{$field}]);
        ActivityLog::record('package.toggled', "Package “{$package->name}” {$field} toggled", $package);

        return back()->with('status', 'Package updated.');
    }

    public function destroy(Package $package): RedirectResponse
    {
        $name = $package->name;
        $package->delete();
        ActivityLog::record('package.deleted', "Package “{$name}” deleted");

        return redirect()->route('admin.packages.index')->with('status', 'Package deleted.');
    }

    /** @return array<string, mixed> */
    private function packageData(PackageRequest $request): array
    {
        $data = $request->safe()->except(['included', 'excluded', 'addon_label', 'addon_price', 'seo_title', 'seo_description']);

        if ($request->boolean('is_custom_pricing')) {
            $data['price_usd'] = null;
        }

        return $data;
    }

    private function syncFeatures(Package $package, PackageRequest $request): void
    {
        $package->features()->delete();
        $order = 0;

        foreach (array_filter((array) $request->input('included', [])) as $label) {
            $package->features()->create(['label' => $label, 'type' => 'included', 'sort_order' => $order++]);
        }
        foreach (array_filter((array) $request->input('excluded', [])) as $label) {
            $package->features()->create(['label' => $label, 'type' => 'excluded', 'sort_order' => $order++]);
        }
        $labels = (array) $request->input('addon_label', []);
        $prices = (array) $request->input('addon_price', []);
        foreach ($labels as $i => $label) {
            if (blank($label)) {
                continue;
            }
            $package->features()->create([
                'label' => $label, 'type' => 'addon',
                'addon_price' => $prices[$i] ?? null, 'sort_order' => $order++,
            ]);
        }
    }

    private function syncSeo(Package $package, PackageRequest $request): void
    {
        if ($request->filled('seo_title') || $request->filled('seo_description')) {
            $package->seo()->updateOrCreate([], [
                'title' => $request->input('seo_title'),
                'description' => $request->input('seo_description'),
            ]);
        }
    }
}
