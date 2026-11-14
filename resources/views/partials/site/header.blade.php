@php
    $brand = $settings['site_name'] ?? 'YourBrand';
    $isActive = fn ($url) => request()->is(trim($url, '/') ?: '/');
@endphp
<header class="site-header">
    <div class="container">
        <div class="nav-bar">
            <a href="{{ url('/') }}" class="brand" aria-label="{{ $brand }} home">
                <span class="dot"></span>{{ $brand }}<span class="suffix">.</span>
            </a>

            <nav aria-label="Primary">
                <ul class="nav-menu">
                    @foreach ($headerMenu as $item)
                        @if ($item->children->isNotEmpty())
                            <li class="has-dd {{ $isActive($item->url) ? 'active' : '' }}">
                                <a href="{{ url($item->url) }}">{{ $item->label }}
                                    <svg viewBox="0 0 12 8" width="11" height="8" style="margin-left:2px"><path d="M1 1l5 5 5-5" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/></svg>
                                </a>
                                <div class="dd">
                                    @foreach ($item->children as $child)
                                        <a href="{{ url($child->url) }}">{{ $child->label }}</a>
                                    @endforeach
                                </div>
                            </li>
                        @else
                            <li class="{{ $isActive($item->url) ? 'active' : '' }}"><a href="{{ url($item->url) }}">{{ $item->label }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </nav>

            <div class="nav-right">
                <a href="{{ url('/contact') }}" class="btn btn-primary nav-cta">Get Your Website <span class="ico" data-icon="arrow" data-size="18"></span></a>
                <button class="nav-toggle" aria-label="Open menu" aria-controls="drawer"><span></span><span></span><span></span></button>
            </div>
        </div>
    </div>
</header>

<div class="drawer-backdrop"></div>
<aside class="drawer" id="drawer" aria-label="Mobile menu">
    <div class="drawer-top">
        <a href="{{ url('/') }}" class="brand"><span class="dot"></span>{{ $brand }}<span class="suffix">.</span></a>
        <button class="drawer-close" aria-label="Close menu">&times;</button>
    </div>
    <ul class="drawer-menu">
        @foreach ($headerMenu as $item)
            <li><a href="{{ url($item->url) }}">{{ $item->label }}</a></li>
            @foreach ($item->children as $child)
                <li class="drawer-sub"><a href="{{ url($child->url) }}">{{ $child->label }}</a></li>
            @endforeach
        @endforeach
    </ul>
    <a href="{{ url('/contact') }}" class="btn btn-primary btn-block">Get Your Website <span class="ico" data-icon="arrow" data-size="18"></span></a>
    @if (!empty($settings['contact_phone']))
        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['contact_phone']) }}" class="btn btn-ghost btn-block" style="margin-top:12px"><span data-icon="phone" data-size="18"></span> {{ $settings['contact_phone'] }}</a>
    @endif
</aside>
