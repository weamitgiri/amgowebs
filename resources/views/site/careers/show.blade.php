@extends('layouts.site')

@php
    $apply = $job->apply_url ?: url('/contact');
    $responsibilities = $job->lines($job->responsibilities);
    $requirements = $job->lines($job->requirements);
@endphp

@section('content')
<section class="page-hero">
    <div class="hero-grid"></div>
    <div class="container">
        <span class="eyebrow" data-reveal>Careers</span>
        <h1 data-reveal data-delay="1">{{ $job->title }}</h1>
        <p data-reveal data-delay="2" style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center">
            <span class="pill">{{ $job->type_label }}</span>
            @if ($job->department)<span class="pill">{{ $job->department }}</span>@endif
            <span class="pill">{{ $job->is_remote ? 'Remote' : $job->location }}</span>
            @if ($job->experience_level)<span class="pill">{{ $job->experience_level }}</span>@endif
        </p>
        <div class="breadcrumb" data-reveal data-delay="3"><a href="{{ url('/') }}">Home</a><span class="sep">/</span><a href="{{ url('/careers') }}">Careers</a><span class="sep">/</span><span>{{ $job->title }}</span></div>
    </div>
</section>
@include('partials.site.breadcrumb-schema', ['crumbs' => [
    ['name' => 'Home', 'url' => url('/')],
    ['name' => 'Careers', 'url' => url('/careers')],
    ['name' => $job->title, 'url' => url('/careers/'.$job->slug)],
]])

<section class="section section--tight">
    <div class="container">
        <div class="prose" style="max-width:760px;margin-inline:auto" data-reveal>
            @if ($job->summary)<p style="font-size:18px;color:var(--text)">{{ $job->summary }}</p>@endif
            @if ($job->description)<p>{!! nl2br(e($job->description)) !!}</p>@endif

            @if ($responsibilities)
                <h2>What you'll do</h2>
                <ul>@foreach ($responsibilities as $r)<li>{{ $r }}</li>@endforeach</ul>
            @endif

            @if ($requirements)
                <h2>What we're looking for</h2>
                <ul>@foreach ($requirements as $r)<li>{{ $r }}</li>@endforeach</ul>
            @endif

            <h2>The details</h2>
            <ul>
                <li><strong>Type:</strong> {{ $job->type_label }}</li>
                <li><strong>Location:</strong> {{ $job->is_remote ? 'Remote — work from anywhere' : $job->location }}</li>
                @if ($job->experience_level)<li><strong>Experience:</strong> {{ $job->experience_level }}</li>@endif
                @if ($job->salary_range)<li><strong>Compensation:</strong> {{ $job->salary_range }}</li>@endif
                @if ($job->posted_at)<li><strong>Posted:</strong> {{ $job->posted_at->format('F j, Y') }}</li>@endif
            </ul>
        </div>

        <div class="cta-band" data-reveal style="max-width:760px;margin:40px auto 0;text-align:center">
            <h2>Interested in this role?</h2>
            <p>Tell us about yourself and share your CV or portfolio — we read every application.</p>
            <div class="cta-actions" style="justify-content:center">
                <a href="{{ $apply }}" @if($job->apply_url) target="_blank" rel="noopener" @endif class="btn btn-primary btn-lg">Apply Now <span class="ico" data-icon="arrow"></span></a>
                <a href="{{ url('/careers') }}" class="btn btn-ghost btn-lg">All Openings</a>
            </div>
        </div>
    </div>
</section>
@endsection
