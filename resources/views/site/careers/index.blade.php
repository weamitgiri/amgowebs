@extends('layouts.site')

@section('content')
<section class="page-hero">
    <div class="hero-grid"></div>
    <div class="container">
        <span class="eyebrow" data-reveal>Careers</span>
        <h1 data-reveal data-delay="1">Build the web with a team of <span class="text-grad">{{ $teamSize }} developers</span></h1>
        <p data-reveal data-delay="2">We're a remote-first web design studio working with businesses all over the world. We're growing — and looking for talented, reliable people to grow with us.</p>
        <div class="breadcrumb" data-reveal data-delay="3"><a href="{{ url('/') }}">Home</a><span class="sep">/</span><span>Careers</span></div>
    </div>
</section>
@include('partials.site.breadcrumb-schema', ['crumbs' => [
    ['name' => 'Home', 'url' => url('/')],
    ['name' => 'Careers', 'url' => url('/careers')],
]])

<section class="section section--tight">
    <div class="container">
        <div class="section-head center" data-reveal>
            <span class="eyebrow eyebrow--violet">Why Join Us</span>
            <h2 class="section-title">A remote team that does real work, the honest way</h2>
            <p class="section-sub">No agency politics, no busywork — just interesting projects, fair pay and a team that respects your time.</p>
        </div>
        <div class="feature-grid">
            <div class="feature" data-reveal data-delay="1"><div class="ic" data-icon="map" data-size="26"></div><h3>Fully Remote</h3><p>Work from anywhere in the world. We care about results, not hours at a desk.</p></div>
            <div class="feature" data-reveal data-delay="2"><div class="ic" data-icon="layout" data-size="26"></div><h3>Real Projects</h3><p>Ship meaningful work for real businesses across many industries and countries.</p></div>
            <div class="feature" data-reveal data-delay="3"><div class="ic" data-icon="trending" data-size="26"></div><h3>Room to Grow</h3><p>Learn from experienced teammates and take on more responsibility as you grow.</p></div>
            <div class="feature" data-reveal data-delay="4"><div class="ic" data-icon="target" data-size="26"></div><h3>Fair & Transparent</h3><p>Clear expectations, honest feedback and fair, competitive pay.</p></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head center" data-reveal>
            <span class="eyebrow">Open Positions</span>
            <h2 class="section-title">Current openings</h2>
        </div>
        @if ($jobs->isNotEmpty())
            <div class="feature-grid">
                @foreach ($jobs as $job)
                    @php $jobMeta = collect([$job->type_label, $job->department ?: null, $job->is_remote ? 'Remote' : null])->filter()->implode(' · '); @endphp
                    <a href="{{ url('/careers/'.$job->slug) }}" class="feature feature--link" data-reveal data-delay="{{ ($loop->index % 4) + 1 }}">
                        <div class="ic" data-icon="briefcase" data-size="26"></div>
                        <h3>{{ $job->title }}</h3>
                        <p style="margin-bottom:6px;color:var(--muted-2);font-size:13px">{{ $jobMeta }}</p>
                        <p>{{ $job->summary }}</p>
                        <span class="feature-more">View role <span class="ico" data-icon="arrow"></span></span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="cta-band" data-reveal style="text-align:center">
                <h3 style="margin-bottom:10px">No open roles right now</h3>
                <p>We're not actively hiring at the moment, but we're always glad to hear from great people. Send us a short intro and your CV and we'll keep you in mind.</p>
                <div class="cta-actions" style="justify-content:center;margin-top:18px"><a href="{{ url('/contact') }}" class="btn btn-primary">Introduce Yourself <span class="ico" data-icon="arrow"></span></a></div>
            </div>
        @endif
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <div class="cta-band" data-reveal>
            <span class="eyebrow">Don't see your role?</span>
            <h2>We're always open to great people</h2>
            <p>If you think you'd be a great fit but don't see the right opening, tell us how you can help and share your portfolio or CV.</p>
            <div class="cta-actions"><a href="{{ url('/contact') }}" class="btn btn-primary btn-lg">Get in Touch <span class="ico" data-icon="arrow"></span></a></div>
        </div>
    </div>
</section>
@endsection
