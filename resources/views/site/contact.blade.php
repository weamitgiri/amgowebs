@extends('layouts.site')

@section('content')
<section class="page-hero" style="padding-bottom:30px">
    <div class="hero-grid"></div>
    <div class="container">
        <span class="eyebrow" data-reveal>Get Your Website</span>
        <h1 data-reveal data-delay="1">Tell us about your <span class="text-grad">project</span></h1>
        <p data-reveal data-delay="2">Choose a package, share your requirements, and a specialist will review and schedule a free 15-minute call. We respond fast.</p>
        <div class="breadcrumb" data-reveal data-delay="3"><a href="{{ url('/') }}">Home</a><span class="sep">/</span><span>Contact</span></div>
    </div>
</section>

<section class="section section--tight">
    <div class="container">
        <div class="contact-grid">
            {{-- Left: contact info + process --}}
            <div data-reveal>
                <span class="eyebrow eyebrow--violet">How it works</span>
                <h2 class="section-title">From inquiry to launch</h2>
                <ul class="inquiry-points">
                    <li><span class="n">1</span><p><b>Submit your requirements</b> Pick a package and describe your project below.</p></li>
                    <li><span class="n">2</span><p><b>Free 15-minute review</b> We review and schedule a quick call to finalise scope.</p></li>
                    <li><span class="n">3</span><p><b>Proposal &amp; build</b> You receive a tailored proposal, then we design and launch.</p></li>
                </ul>
                <div class="contact-cards">
                    @if (!empty($settings['contact_email']))
                        <a class="contact-card" href="mailto:{{ $settings['contact_email'] }}"><span class="cc-ic" data-icon="mail"></span><span><b>Email Us</b><span>{{ $settings['contact_email'] }}</span></span></a>
                    @endif
                    @if (!empty($settings['contact_phone']))
                        <a class="contact-card" href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['contact_phone']) }}"><span class="cc-ic" data-icon="phone"></span><span><b>Call Us</b><span>{{ $settings['contact_phone'] }}</span></span></a>
                    @endif
                </div>
            </div>

            {{-- Right: inquiry form --}}
            <div class="form-card" data-reveal data-delay="1" id="inquiry-form">
                <h3>Project Inquiry</h3>
                <p class="fc-sub">Fields marked <span style="color:var(--pink)">•</span> are required. We'll never share your details.</p>

                @if ($errors->any())
                    <div class="alert-error">Please fix the highlighted fields and try again.</div>
                @endif

                <form method="POST" action="{{ route('inquiry.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Honeypot (hidden from humans) --}}
                    <div style="position:absolute;left:-9999px" aria-hidden="true">
                        <label>Do not fill<input type="text" name="nickname" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <div class="field">
                        <label for="name">Full Name <span style="color:var(--pink)">•</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required placeholder="Jane Doe">
                        @error('name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="business_name">Business Name</label>
                        <input id="business_name" name="business_name" type="text" value="{{ old('business_name') }}" placeholder="Acme Inc.">
                    </div>

                    <div class="grid" style="grid-template-columns:1fr 1fr;gap:16px">
                        <div class="field">
                            <label for="email">Email <span style="color:var(--pink)">•</span></label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required placeholder="jane@email.com">
                            @error('email')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="phone">Phone / Telegram</label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="+1 555 000 0000">
                        </div>
                    </div>

                    <div class="grid" style="grid-template-columns:1fr 1fr;gap:16px">
                        <div class="field">
                            <label for="country">Country <span style="color:var(--pink)">•</span></label>
                            <select id="country" name="country" required>
                                <option value="" disabled {{ old('country') ? '' : 'selected' }}>Select country</option>
                                @foreach ($countries as $c)
                                    <option value="{{ $c }}" @selected(old('country') === $c)>{{ $c }}</option>
                                @endforeach
                            </select>
                            @error('country')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="package_id">Package</label>
                            <select id="package_id" name="package_id">
                                <option value="">General enquiry</option>
                                @foreach ($packages as $pkg)
                                    <option value="{{ $pkg->id }}" @selected((int) old('package_id', $selectedPackageId) === $pkg->id)>{{ $pkg->name }} ({{ $pkg->price_label }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid" style="grid-template-columns:1fr 1fr;gap:16px">
                        <div class="field">
                            <label for="website_type">Website Type</label>
                            <select id="website_type" name="website_type">
                                <option value="">Select type</option>
                                @foreach ($websiteTypes as $wt)
                                    <option value="{{ $wt }}" @selected(old('website_type') === $wt)>{{ $wt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="pages_required">Pages Required</label>
                            <select id="pages_required" name="pages_required">
                                <option value="">Select</option>
                                @foreach ($pageOptions as $po)
                                    <option value="{{ $po }}" @selected(old('pages_required') === $po)>{{ $po }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label for="existing_url">Existing Website (if any)</label>
                        <input id="existing_url" name="existing_url" type="url" value="{{ old('existing_url') }}" placeholder="https://yoursite.com">
                        @error('existing_url')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="description">Project Description <span style="color:var(--pink)">•</span></label>
                        <textarea id="description" name="description" required placeholder="Tell us about your business, goals, and what you need...">{{ old('description') }}</textarea>
                        @error('description')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="required_features">Required Features</label>
                        <textarea id="required_features" name="required_features" placeholder="e.g. contact form, booking, blog, payments...">{{ old('required_features') }}</textarea>
                    </div>

                    <div class="grid" style="grid-template-columns:1fr 1fr;gap:16px">
                        <div class="field">
                            <label for="budget">Budget</label>
                            <select id="budget" name="budget">
                                <option value="">Select budget</option>
                                @foreach ($budgets as $b)
                                    <option value="{{ $b }}" @selected(old('budget') === $b)>{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="preferred_meeting_at">Preferred Meeting</label>
                            <input id="preferred_meeting_at" name="preferred_meeting_at" type="datetime-local" value="{{ old('preferred_meeting_at') }}">
                            @error('preferred_meeting_at')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="field">
                        <label for="additional_requirements">Additional Requirements</label>
                        <textarea id="additional_requirements" name="additional_requirements" placeholder="Anything else we should know?">{{ old('additional_requirements') }}</textarea>
                    </div>

                    <div class="field">
                        <label for="attachment">Upload Requirements File <span style="color:var(--muted-2);font-weight:400">(optional)</span></label>
                        <input id="attachment" name="attachment" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.rtf,.odt,.jpg,.jpeg,.png,.webp">
                        <small style="display:block;margin-top:6px;color:var(--muted-2);font-size:12px">Share a brief, mockup, logo or requirements document — PDF, Word, Excel, text or image, up to 10&nbsp;MB.</small>
                        @error('attachment')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <label class="consent">
                        <input type="checkbox" name="consent" value="1" {{ old('consent') ? 'checked' : '' }} required>
                        I agree to be contacted about this inquiry and consent to my details being stored for follow-up.
                    </label>
                    @error('consent')<div class="field-error">{{ $message }}</div>@enderror

                    <button type="submit" class="btn btn-primary btn-block btn-lg">Submit Inquiry <span class="ico" data-icon="arrow"></span></button>
                    <div class="form-badges">
                        <span><span data-icon="shield" data-size="15"></span> 100% Confidential</span>
                        <span><span data-icon="clock" data-size="15"></span> Fast Response</span>
                        <span><span data-icon="check" data-size="15"></span> No Obligation</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
