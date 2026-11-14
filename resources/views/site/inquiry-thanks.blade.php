@extends('layouts.site')

@section('content')
<section class="page-hero" style="min-height:60vh;display:grid;place-items:center">
    <div class="hero-grid"></div>
    <div class="container" style="max-width:640px">
        <div class="hero-visual" style="max-width:120px;margin:0 auto 20px" aria-hidden="true">
            <div class="orbit o1"><span class="sat"></span></div>
            <div class="core" style="width:60%"></div>
        </div>
        <span class="eyebrow" data-reveal>Inquiry Received</span>
        <h1 data-reveal data-delay="1">Thank you, {{ $inquiry->name }}! <span class="text-grad">🎉</span></h1>
        <p data-reveal data-delay="2">Your inquiry has been received. Our team will review your requirements and reach out to schedule your free 15-minute review meeting.</p>

        <div class="ref-box" data-reveal data-delay="2" style="margin:26px auto;max-width:360px">
            {{ $inquiry->reference }}
            <div style="font-size:12px;font-weight:500;letter-spacing:0;color:var(--muted-2);margin-top:6px">Save this reference number</div>
        </div>

        <div style="text-align:left;max-width:420px;margin:0 auto 28px" data-reveal data-delay="3">
            <ul class="check-list">
                @if ($inquiry->package)<li><span class="ck" data-icon="check" data-size="16"></span> Package: {{ $inquiry->package->name }}</li>@endif
                <li><span class="ck" data-icon="check" data-size="16"></span> Confirmation sent to {{ $inquiry->email }}</li>
                <li><span class="ck" data-icon="check" data-size="16"></span> Our team reviews &amp; schedules your call</li>
            </ul>
        </div>

        <div class="flex-cta" style="justify-content:center" data-reveal data-delay="3">
            <a href="{{ url('/') }}" class="btn btn-primary btn-lg">Back to Home <span class="ico" data-icon="arrow"></span></a>
            <a href="{{ url('/blog') }}" class="btn btn-ghost btn-lg">Read our blog</a>
        </div>
    </div>
</section>
@endsection
