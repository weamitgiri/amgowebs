@extends('layouts.site')

@php
    // Admin-editable homepage content (managed via Pages → Home sections).
    // Every block falls back to a sensible default so the page never renders empty.
    $hero = $page->sectionData('hero');
    $heroBg = trim((string) ($hero['background_image'] ?? ''));
    $heroBg = preg_match('~^(https?://|/)[^\s\'"()<>]+$~i', $heroBg) ? $heroBg : '';
    $heroStyle = $heroBg ? "background-image:linear-gradient(rgba(5,6,15,.74),rgba(5,6,15,.88)),url('{$heroBg}');background-size:cover;background-position:center" : '';
    $why = $page->sectionData('features');
    $testi = $page->sectionData('testimonials');
    $faq = $page->sectionData('faq');
    $cta = $page->sectionData('cta');

    $whyItems = $why['items'] ?? [
        ['icon' => 'gauge', 'title' => 'Fast, Reliable Delivery', 'text' => 'Most websites go live in 1–3 weeks, hand-built with clean, fast, mobile-first code.'],
        ['icon' => 'target', 'title' => 'Transparent Fixed Pricing', 'text' => 'Clear one-time prices with no monthly lock-in and no surprise fees — you know exactly what you pay.'],
        ['icon' => 'search', 'title' => 'SEO-Ready From Day One', 'text' => 'Every site ships with clean markup, titles, meta tags and fast load times so Google can find you.'],
        ['icon' => 'shield', 'title' => 'You Own Everything', 'text' => 'Your website, code and content stay yours. Host it anywhere, with friendly support after launch.'],
    ];

    $faqItems = $faq['items'] ?? [
        ['question' => 'How much does a professional website cost?', 'answer' => 'Our fixed packages start at $199 for a 5-page Starter site and go up to $599 for a 20-page Premium site. Custom and e-commerce projects are quoted to fit your scope. Agencies in the USA and UK typically charge several thousand dollars for comparable work.'],
        ['question' => 'How long will my website take?', 'answer' => 'Starter sites are usually delivered in about 7 days, Business sites in around 12 days, and Premium sites in roughly 3 weeks, depending on how quickly you share content and feedback.'],
        ['question' => 'Do I own my website once it is built?', 'answer' => 'Yes. You own your website, its code and all content outright. There is no monthly lock-in — you can host it anywhere, and we will help you set it up.'],
        ['question' => 'Which countries do you work with?', 'answer' => 'Every country. We are a remote-first studio and work with businesses anywhere in the world — all projects are handled online, so your location is never a barrier.'],
    ];
@endphp

@section('content')
<!-- HERO -->
<section class="hero{{ $heroBg ? ' hero--image' : '' }}" style="{!! $heroStyle !!}">
    <div class="hero-grid"></div>
    <div class="container">
        <div class="hero-copy">
            <span class="eyebrow" data-reveal>{{ $hero['eyebrow'] ?? 'Affordable · Professional · Global' }}</span>
            @if (!empty($hero['heading']))
                <h1 data-reveal data-delay="1">{{ $hero['heading'] }}@if (!empty($hero['highlight'])) <span class="text-grad">{{ $hero['highlight'] }}</span>@endif</h1>
            @else
                <h1 data-reveal data-delay="1">Professional Websites at <span class="text-grad">Affordable Prices</span></h1>
            @endif
            <p class="lead" data-reveal data-delay="2">{{ $hero['subheading'] ?? 'Launch your business online with a professionally designed, mobile-ready website — transparent fixed pricing, fast delivery, and reliable support. We work with businesses worldwide, in every country.' }}</p>
            <div class="hero-actions" data-reveal data-delay="3">
                <a href="{{ $hero['primary_url'] ?? url('/contact') }}" class="btn btn-primary btn-lg">{{ $hero['primary_label'] ?? 'Get Your Website' }} <span class="ico" data-icon="arrow"></span></a>
                <a href="{{ $hero['secondary_url'] ?? url('/pricing') }}" class="btn btn-ghost btn-lg">{{ $hero['secondary_label'] ?? 'Explore Our Packages' }}</a>
            </div>
            <div class="hero-trust" data-reveal data-delay="4">
                <div class="av"><span>$</span></div>
                <div><b style="color:#fff;font-family:var(--font-head)">From ${{ number_format((float) ($packages->where('is_custom_pricing', false)->min('price_usd') ?: 199), 0) }}</b> · fixed, one-time pricing — no lock-in, no surprises</div>
            </div>
            <div class="hero-trust" data-reveal data-delay="4" style="margin-top:12px">
                <div class="av"><span data-icon="users" data-size="18"></span></div>
                <div>A team of <b style="color:#fff;font-family:var(--font-head)">{{ $teamSize }} developers</b> · building fast, modern websites for businesses worldwide</div>
            </div>
        </div>
        <div class="hero-visual" data-reveal data-delay="2" aria-hidden="true">
            <div class="orbit o1"><span class="sat"></span></div>
            <div class="orbit o2"><span class="sat"></span></div>
            <div class="orbit o3"><span class="sat"></span></div>
            <div class="core"></div>
            <span class="hero-chip chip-1"><span data-icon="gauge" data-size="15"></span> Fast &amp; secure</span>
            <span class="hero-chip chip-2"><span data-icon="search" data-size="15"></span> SEO-ready</span>
            <span class="hero-chip chip-3"><span data-icon="mobile" data-size="15"></span> Responsive</span>
        </div>
    </div>
    <div class="hero-scroll" aria-hidden="true">SCROLL</div>
</section>

<!-- MARQUEE -->
<div class="marquee" aria-hidden="true">
    <div class="marquee-track">
        <span>Web Design &nbsp; Responsive &nbsp; SEO-Ready &nbsp; Fast Delivery &nbsp; Affordable &nbsp; Global Support &nbsp; E-Commerce &nbsp; Website Redesign &nbsp;</span>
        <span>Web Design &nbsp; Responsive &nbsp; SEO-Ready &nbsp; Fast Delivery &nbsp; Affordable &nbsp; Global Support &nbsp; E-Commerce &nbsp; Website Redesign &nbsp;</span>
    </div>
</div>

<!-- WHAT WE DO -->
<section class="section">
    <div class="container">
        <div class="section-head center" data-reveal>
            <span class="eyebrow eyebrow--violet">What We Do</span>
            <h2 class="section-title">Websites and web services that grow your business</h2>
            <p class="section-sub">From your first business website to an online store, a redesign or ongoing care — all at honest, fixed prices, for businesses in any country.</p>
        </div>
        <div class="feature-grid">
            <a href="{{ url('/website-design') }}" class="feature feature--link" data-reveal data-delay="1"><div class="ic" data-icon="brush" data-size="26"></div><h3>Website Design</h3><p>Custom, mobile-first design that builds trust and turns visitors into enquiries.</p><span class="feature-more">Learn more <span class="ico" data-icon="arrow"></span></span></a>
            <a href="{{ url('/ecommerce-website-development') }}" class="feature feature--link" data-reveal data-delay="2"><div class="ic" data-icon="trending" data-size="26"></div><h3>E-Commerce</h3><p>Sell online with clear product pages, secure checkout and a dashboard you control.</p><span class="feature-more">Learn more <span class="ico" data-icon="arrow"></span></span></a>
            <a href="{{ url('/website-redesign') }}" class="feature feature--link" data-reveal data-delay="3"><div class="ic" data-icon="layout" data-size="26"></div><h3>Website Redesign</h3><p>Modernise a slow or dated site into a fast, high-converting one — without losing your SEO.</p><span class="feature-more">Learn more <span class="ico" data-icon="arrow"></span></span></a>
            <a href="{{ url('/seo-services') }}" class="feature feature--link" data-reveal data-delay="4"><div class="ic" data-icon="search" data-size="26"></div><h3>SEO Services</h3><p>The on-page and technical foundations that help Google find and understand your site.</p><span class="feature-more">Learn more <span class="ico" data-icon="arrow"></span></span></a>
            <a href="{{ url('/website-maintenance') }}" class="feature feature--link" data-reveal data-delay="1"><div class="ic" data-icon="shield" data-size="26"></div><h3>Maintenance &amp; Support</h3><p>Updates, backups, fixes and friendly help that keep your site fast and secure.</p><span class="feature-more">Learn more <span class="ico" data-icon="arrow"></span></span></a>
            <a href="{{ url('/industries') }}" class="feature feature--link" data-reveal data-delay="2"><div class="ic" data-icon="star" data-size="26"></div><h3>Industries We Serve</h3><p>Websites tailored to restaurants, clinics, trades, startups, online stores and more.</p><span class="feature-more">Learn more <span class="ico" data-icon="arrow"></span></span></a>
        </div>
        <div class="stats-cta" data-reveal style="margin-top:40px">
            <a href="{{ url('/portfolio') }}" class="btn btn-primary">See Our Work <span class="ico" data-icon="arrow"></span></a>
            <a href="{{ url('/services') }}" class="btn btn-outline">All Services</a>
        </div>
    </div>
</section>

<!-- STATS -->
@include('partials.site.sections.stats', ['data' => [
    'eyebrow' => 'By the Numbers',
    'heading' => 'A real team behind every website',
    'items' => [
        ['value' => $teamSize, 'suffix' => '', 'label' => 'In-house developers'],
        ['value' => '$199', 'suffix' => '', 'label' => 'Starting price, one-time'],
        ['value' => '1–3', 'suffix' => ' wks', 'label' => 'Typical delivery time'],
        ['value' => 'Global', 'suffix' => '', 'label' => 'Clients, in any country'],
    ],
]])

<!-- PACKAGES -->
<section class="section" id="packages">
    <div class="container">
        <div class="section-head center" data-reveal>
            <span class="eyebrow">Website Packages</span>
            <h2 class="section-title">Transparent pricing, built for your budget</h2>
            <p class="section-sub">Fixed, one-time website packages with everything you need to launch. No monthly fees, no hidden costs.</p>
        </div>
        <div class="pricing-grid">
            @foreach ($packages as $package)
                @include('partials.site.package-card')
            @endforeach
        </div>
        <div class="stats-cta" data-reveal style="margin-top:40px">
            <a href="{{ url('/pricing') }}" class="btn btn-primary">Compare All Packages <span class="ico" data-icon="arrow"></span></a>
        </div>
    </div>
</section>

<!-- WHY CHOOSE -->
<section class="section section--dark">
    <div class="container">
        <div class="section-head center" data-reveal>
            <span class="eyebrow eyebrow--violet">{{ $why['eyebrow'] ?? 'Why Choose Us' }}</span>
            <h2 class="section-title">{{ $why['heading'] ?? 'Everything you need to succeed online' }}</h2>
        </div>
        <div class="feature-grid">
            @foreach ($whyItems as $i => $item)
                <div class="feature" data-reveal data-delay="{{ ($i % 4) + 1 }}"><div class="ic" data-icon="{{ $item['icon'] ?? 'check' }}" data-size="26"></div><h3>{{ $item['title'] ?? '' }}</h3><p>{{ $item['text'] ?? '' }}</p></div>
            @endforeach
        </div>
    </div>
</section>

<!-- PROCESS -->
<section class="section">
    <div class="container">
        <div class="section-head center" data-reveal>
            <span class="eyebrow">How It Works</span>
            <h2 class="section-title">From inquiry to launch in 4 simple steps</h2>
            <p class="section-sub">A clear, low-pressure process — and a free 15-minute review call before any work begins.</p>
        </div>
        <div class="process-grid">
            <div class="pstep" data-reveal data-delay="1"><div class="pnum">01</div><h4>Submit Requirements</h4><p>Pick a package and tell us about your business and goals — it takes two minutes.</p></div>
            <div class="pstep" data-reveal data-delay="2"><div class="pnum">02</div><h4>15-Min Review Call</h4><p>We confirm scope, answer questions and agree on a clear timeline and fixed price.</p></div>
            <div class="pstep" data-reveal data-delay="3"><div class="pnum">03</div><h4>Design &amp; Build</h4><p>We design and develop your responsive, SEO-ready website and share it for review.</p></div>
            <div class="pstep" data-reveal data-delay="4"><div class="pnum">04</div><h4>Launch &amp; Support</h4><p>We launch your site, hand over full ownership, and support you after go-live.</p></div>
        </div>
        <div class="stats-cta" data-reveal style="margin-top:36px">
            <a href="{{ url('/how-it-works') }}" class="btn btn-outline">See our full process <span class="ico" data-icon="arrow"></span></a>
        </div>
    </div>
</section>

@if (!empty($testi['items']))
<!-- TESTIMONIALS (shown only when real reviews are added in the admin) -->
<section class="section section--dark">
    <div class="container">
        <div class="section-head center" data-reveal>
            <span class="eyebrow">{{ $testi['eyebrow'] ?? 'Client Love' }}</span>
            <h2 class="section-title">{{ $testi['heading'] ?? 'What our clients say' }}</h2>
        </div>
        <div class="testi-grid">
            @foreach ($testi['items'] as $i => $item)
                <div class="testi" data-reveal data-delay="{{ ($i % 3) + 1 }}"><div class="stars">★★★★★</div><p>"{{ $item['quote'] ?? '' }}"</p><div class="who"><span class="av">{{ strtoupper(substr($item['name'] ?? 'A', 0, 1)) }}</span><span><b>{{ $item['name'] ?? '' }}</b><span>{{ $item['role'] ?? '' }}</span></span></div></div>
            @endforeach
        </div>
    </div>
</section>
@else
<!-- OUR PROMISE (honest value props — no fabricated reviews) -->
<section class="section section--dark">
    <div class="container">
        <div class="section-head center" data-reveal>
            <span class="eyebrow">Our Promise</span>
            <h2 class="section-title">A simple, honest way to get your website</h2>
            <p class="section-sub">No jargon, no lock-in, no pressure — just a professional website done right.</p>
        </div>
        <div class="feature-grid">
            <div class="feature" data-reveal data-delay="1"><div class="ic" data-icon="target" data-size="26"></div><h3>Fixed Price, In Writing</h3><p>You approve the scope and price before we start. What we quote is what you pay.</p></div>
            <div class="feature" data-reveal data-delay="2"><div class="ic" data-icon="gauge" data-size="26"></div><h3>On-Time Delivery</h3><p>We agree a timeline up front and keep you updated at every stage until launch.</p></div>
            <div class="feature" data-reveal data-delay="3"><div class="ic" data-icon="shield" data-size="26"></div><h3>Full Ownership</h3><p>You own the finished website, code and content — with no monthly ransom to keep it online.</p></div>
            <div class="feature" data-reveal data-delay="4"><div class="ic" data-icon="phone" data-size="26"></div><h3>Real Human Support</h3><p>Talk to the people building your site, before and after launch. No call-centre scripts.</p></div>
        </div>
    </div>
</section>
@endif

@if ($posts->isNotEmpty())
<!-- BLOG TEASER -->
<section class="section">
    <div class="container">
        <div class="section-head center" data-reveal>
            <span class="eyebrow eyebrow--violet">From the Blog</span>
            <h2 class="section-title">Tips to grow your business online</h2>
        </div>
        <div class="blog-grid">
            @foreach ($posts as $post)
                <a class="blog-card" href="{{ route('blog.show', $post->slug) }}" data-reveal>
                    <div class="thumb"><span class="cat">{{ $post->category?->name ?? 'Article' }}</span><span data-icon="brush" data-size="40" style="color:var(--cyan);opacity:.4"></span></div>
                    <div class="bc-body">
                        <div class="meta">{{ $post->published_at?->format('M j, Y') }} · {{ $post->reading_time }} min read</div>
                        <h3>{{ $post->title }}</h3>
                        <p>{{ $post->excerpt }}</p>
                        <span class="read">Read Article <span data-icon="arrow" data-size="15"></span></span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="stats-cta" data-reveal style="margin-top:36px">
            <a href="{{ route('blog.index') }}" class="btn btn-outline">Read the blog <span class="ico" data-icon="arrow"></span></a>
        </div>
    </div>
</section>
@endif

<!-- FAQ (emits FAQPage schema from visible Q&A) -->
@include('partials.site.sections.faq', ['data' => [
    'eyebrow' => $faq['eyebrow'] ?? 'Questions',
    'heading' => $faq['heading'] ?? 'Frequently asked questions',
    'items' => $faqItems,
]])

<!-- CTA -->
<section class="section section--tight">
    <div class="container">
        <div class="cta-band" data-reveal>
            <span class="eyebrow">{{ $cta['eyebrow'] ?? 'Ready to Launch?' }}</span>
            <h2>{{ $cta['heading'] ?? 'Get your professional website today' }}</h2>
            <p>{{ $cta['text'] ?? 'Submit your requirements and get a free quote plus a 15-minute review meeting — no obligation.' }}</p>
            <div class="cta-actions">
                <a href="{{ $cta['button_url'] ?? url('/contact') }}" class="btn btn-primary btn-lg">{{ $cta['button_label'] ?? 'Get Your Website' }} <span class="ico" data-icon="arrow"></span></a>
                <a href="{{ url('/pricing') }}" class="btn btn-ghost btn-lg">View Packages</a>
            </div>
        </div>
    </div>
</section>
@endsection
