<?php

namespace App\View\Composers;

use App\Models\Menu;
use App\Models\Setting;
use Illuminate\View\View;

class SiteComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'settings' => Setting::allSettings(),
            'headerMenu' => $this->menu('header'),
            'footerMenu' => $this->menu('footer'),
        ]);
    }

    private function menu(string $location)
    {
        return Menu::location($location)
            ->active()
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();
    }
}
