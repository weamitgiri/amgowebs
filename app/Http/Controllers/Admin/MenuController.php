<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        return view('admin.menus.index', [
            'header' => Menu::location('header')->orderBy('sort_order')->get(),
            'footer' => Menu::location('footer')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        Menu::create($data);
        ActivityLog::record('menu.created', "Menu item “{$data['label']}” added to {$data['location']}");

        return back()->with('status', 'Menu item added.');
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $menu->update($this->validateData($request));

        return back()->with('status', 'Menu item updated.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();
        ActivityLog::record('menu.deleted', "Menu item “{$menu->label}” removed");

        return back()->with('status', 'Menu item removed.');
    }

    /** @return array<string, mixed> */
    private function validateData(Request $request): array
    {
        return [
            ...$request->validate([
                'location' => ['required', 'in:header,footer'],
                'label' => ['required', 'string', 'max:100'],
                'url' => ['required', 'string', 'max:255'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ]),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
