@php
    $brand = $settings['site_name'] ?? 'YourBrand';
    $socials = array_filter([
        'facebook' => $settings['social_facebook'] ?? null,
        'instagram' => $settings['social_instagram'] ?? null,
        'linkedin' => $settings['social_linkedin'] ?? null,
        'x' => $settings['social_x'] ?? null,
        'youtube' => $settings['social_youtube'] ?? null,
    ], fn ($v) => $v && $v !== '#');
    $phone = $settings['contact_phone'] ?? null;
    $email = $settings['contact_email'] ?? null;
    $tg = trim((string) ($settings['telegram'] ?? ''));
    $tgUrl = $tg === '' ? null
        : (\Illuminate\Support\Str::startsWith($tg, ['http://', 'https://']) ? $tg : 'https://t.me/'.ltrim($tg, '@/'));
@endphp
<div class="footer-marquee" aria-hidden="true">
    <div class="marquee-track">
        <span>AFFORDABLE WEBSITES &nbsp; TRANSPARENT PRICING &nbsp; GLOBAL SUPPORT &nbsp; LAUNCH &amp; GROW &nbsp;</span>
        <span>AFFORDABLE WEBSITES &nbsp; TRANSPARENT PRICING &nbsp; GLOBAL SUPPORT &nbsp; LAUNCH &amp; GROW &nbsp;</span>
    </div>
</div>

<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <a href="{{ url('/') }}" class="brand"><span class="dot"></span>{{ $brand }}<span class="suffix">.</span></a>
                <p>{{ $settings['site_tagline'] ?? 'Professional websites at affordable prices' }}. We help businesses around the world launch online with transparent pricing and reliable support — wherever you are.</p>
                @if ($socials)
                    <div class="footer-social">
                        @foreach ($socials as $net => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($net) }}"><span data-icon="{{ $net }}" data-size="18"></span></a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="footer-col">
                <h5>Quick Links</h5>
                <ul>
                    @foreach ($footerMenu as $item)
                        <li><a href="{{ url($item->url) }}">{{ $item->label }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="footer-col">
                <h5>Our Services</h5>
                <ul>
                    <li><a href="{{ url('/website-design') }}">Website Design</a></li>
                    <li><a href="{{ url('/ecommerce-website-development') }}">E-Commerce Development</a></li>
                    <li><a href="{{ url('/website-redesign') }}">Website Redesign</a></li>
                    <li><a href="{{ url('/seo-services') }}">SEO Services</a></li>
                    <li><a href="{{ url('/website-maintenance') }}">Maintenance &amp; Support</a></li>
                </ul>
            </div>

            <div class="footer-col footer-contact-col">
                <h5>Get in touch</h5>
                <div class="footer-contact">
                    @if ($phone)<a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}"><span class="fi" data-icon="phone" data-size="18"></span> {{ $phone }}</a>@endif
                    @if ($email)<a href="mailto:{{ $email }}"><span class="fi" data-icon="mail" data-size="18"></span> {{ $email }}</a>@endif
                    @if ($tgUrl)<a href="{{ $tgUrl }}" target="_blank" rel="noopener"><span class="fi" data-icon="telegram" data-size="18"></span> Message on Telegram</a>@endif
                    @if (!empty($settings['address']))<a href="#"><span class="fi" data-icon="location" data-size="18"></span> {{ $settings['address'] }}</a>@endif
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ $brand }}. All rights reserved.</p>
            <div class="fb-links">
                <a href="{{ url('/privacy') }}">Privacy Policy</a>
                <a href="{{ url('/terms') }}">Terms of Service</a>
                <a href="{{ url('/contact') }}">Contact</a>
            </div>
        </div>
    </div>
</footer>
