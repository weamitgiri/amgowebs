<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        // Retire the discontinued Server Hosting page (a 301 redirect is seeded separately).
        $legacy = Page::where('slug', 'server-hosting')->first();
        if ($legacy) {
            $legacy->sections()->delete();
            $legacy->seo()->delete();
            $legacy->delete();
        }

        foreach ($this->pages() as $data) {
            $sections = $data['sections'] ?? [];
            $seo = $data['seo'] ?? [];
            unset($data['sections'], $data['seo']);

            $page = Page::updateOrCreate(['slug' => $data['slug']], array_merge([
                'template' => 'default',
                'status' => 'published',
                'is_system' => true,
                'is_in_sitemap' => true,
                'published_at' => now(),
            ], $data));

            // Idempotent: rebuild sections from scratch each run.
            $page->sections()->delete();
            foreach (array_values($sections) as $i => $section) {
                $page->sections()->create([
                    'type' => $section['type'],
                    'data' => $section['data'] ?? [],
                    'sort_order' => $i,
                    'is_visible' => true,
                ]);
            }

            $page->seo()->updateOrCreate([], array_merge([
                'robots' => 'index, follow',
            ], $seo));
        }
    }

    private function pages(): array
    {
        return [
            // ---------------------------------------------------------------- HOME
            [
                'title' => 'Home',
                'slug' => 'home',
                'excerpt' => 'Professional, affordable websites for growing businesses — fixed pricing, fast delivery, full ownership.',
                'seo' => [
                    'title' => 'Professional Websites at Affordable Prices',
                    'description' => 'Get a professionally designed, mobile-ready website from $199. Fixed one-time pricing, fast delivery, SEO-ready, full ownership. Serving businesses worldwide — in every country.',
                    'focus_keyword' => 'affordable website design',
                ],
                'sections' => [
                    ['type' => 'hero', 'data' => [
                        'eyebrow' => 'Affordable · Professional · Global',
                        'heading' => 'Professional Websites at',
                        'highlight' => 'Affordable Prices',
                        'subheading' => 'Launch your business online with a professionally designed, mobile-ready website — transparent fixed pricing, fast delivery, and reliable support. We work with businesses worldwide, in every country.',
                        'primary_label' => 'Get Your Website', 'primary_url' => '/contact',
                        'secondary_label' => 'Explore Our Packages', 'secondary_url' => '/pricing',
                    ]],
                    ['type' => 'features', 'data' => [
                        'eyebrow' => 'Why Choose Us',
                        'heading' => 'Everything you need to succeed online',
                        'items' => [
                            ['icon' => 'gauge', 'title' => 'Fast, Reliable Delivery', 'text' => 'Most websites go live in 1–3 weeks, hand-built with clean, fast, mobile-first code.'],
                            ['icon' => 'target', 'title' => 'Transparent Fixed Pricing', 'text' => 'Clear one-time prices with no monthly lock-in and no surprise fees — you know exactly what you pay.'],
                            ['icon' => 'search', 'title' => 'SEO-Ready From Day One', 'text' => 'Every site ships with clean markup, titles, meta tags and fast load times so Google can find you.'],
                            ['icon' => 'shield', 'title' => 'You Own Everything', 'text' => 'Your website, code and content stay yours. Host it anywhere, with friendly support after launch.'],
                        ],
                    ]],
                    ['type' => 'faq', 'data' => [
                        'eyebrow' => 'Questions',
                        'heading' => 'Frequently asked questions',
                        'items' => [
                            ['question' => 'How much does a professional website cost?', 'answer' => 'Our fixed packages start at $199 for a 5-page Starter site and go up to $599 for a 20-page Premium site. Custom and e-commerce projects are quoted to fit. Agencies in the USA and UK typically charge several thousand dollars for comparable work.'],
                            ['question' => 'How long will my website take?', 'answer' => 'Starter sites are usually delivered in about 7 days, Business sites in around 12 days, and Premium sites in roughly 3 weeks — depending on how quickly you share content and feedback.'],
                            ['question' => 'Do I own my website once it is built?', 'answer' => 'Yes. You own your website, its code and all content outright. There is no monthly lock-in — you can host it anywhere, and we will help you set it up.'],
                            ['question' => 'Which countries do you work with?', 'answer' => 'Every country. We are a remote-first studio and work with businesses anywhere in the world — every project is handled online, so your location is never a barrier.'],
                            ['question' => 'Is my website good for SEO?', 'answer' => 'Yes. Every website we build includes on-page SEO fundamentals — clean titles and meta descriptions, logical headings, fast performance, an XML sitemap and structured data — so search engines can crawl and understand your pages from day one.'],
                            ['question' => 'Will my website work on mobile phones?', 'answer' => 'Always. Every site is mobile-first and tested across phones, tablets and desktops, with fast load times and accessible, readable layouts.'],
                            ['question' => 'Do you provide hosting and a domain?', 'answer' => 'We focus on design and development rather than reselling hosting, so your website stays in your own name and you can host it wherever you prefer. We will guide you through choosing a reliable host and connecting your domain at no extra charge.'],
                            ['question' => 'Can you redesign my existing website?', 'answer' => 'Yes. We can rebuild an outdated, slow or hard-to-edit site into a modern, fast one while preserving your existing URLs and search rankings wherever possible.'],
                            ['question' => 'How do I get started?', 'answer' => 'Share your requirements through our short project form and we will reply quickly with a free quote and a 15-minute review call to confirm scope, timeline and a fixed price before any work begins.'],
                        ],
                    ]],
                    ['type' => 'cta', 'data' => [
                        'eyebrow' => 'Ready to Launch?',
                        'heading' => 'Get your professional website today',
                        'text' => 'Submit your requirements and get a free quote plus a 15-minute review meeting — no obligation.',
                        'button_label' => 'Get Your Website', 'button_url' => '/contact',
                    ]],
                ],
            ],

            // ---------------------------------------------------------------- ABOUT
            [
                'title' => 'About Us',
                'slug' => 'about',
                'excerpt' => 'We build professional, affordable websites for small businesses worldwide — with transparent pricing and no jargon.',
                'seo' => [
                    'title' => 'About Us — Affordable Website Design Agency',
                    'description' => 'Learn who we are: a remote-first web design studio helping small businesses around the world launch professional websites at honest, fixed prices — in any country.',
                    'focus_keyword' => 'affordable web design agency',
                ],
                'sections' => [
                    ['type' => 'richtext', 'data' => [
                        'eyebrow' => 'About Us',
                        'heading' => 'Affordable websites, built the honest way',
                        'width' => 'normal',
                        'body' => '<p>We started with a simple belief: every small business deserves a professional website without the mystery pricing, the endless upsells, or the monthly lock-in. For years, getting a decent website meant either wrestling with a clunky DIY builder or paying an agency several thousand dollars for work you never really understood — or owned.</p>'
                            .'<p>We do things differently. We design and build fast, mobile-ready, search-friendly websites at <a href="/pricing">clear fixed prices</a>, hand them over in full, and stick around to help afterwards. No retainers you can\'t escape. No surprise invoices. No code you\'re not allowed to touch.</p>'
                            .'<h3>A real team behind your project</h3>'
                            .'<p>We\'re a growing studio of 50+ developers, designers and specialists working remotely across time zones. That means we can match the right people to your project, keep delivery fast, and give you a real point of contact — not an anonymous queue. We\'re also <a href="/careers">hiring</a> as we grow.</p>'
                            .'<h3>Who we work with</h3>'
                            .'<p>We partner with founders, freelancers, local service businesses, consultants and small teams all over the world — from North America and Europe to Asia, the Middle East, Africa and Oceania. Whether you need a simple 5-page brochure site or a 20-page business website with a blog, we match the work to your goals and your budget — and tell you honestly when you need less than you think.</p>'
                            .'<h3>Affordable is not the same as cheap</h3>'
                            .'<p>Affordable means fair, transparent pricing and no waste. It does not mean cutting corners. Every site we build is responsive, accessible, optimised for Core Web Vitals, and set up with the on-page SEO basics that help you get found on Google. See exactly <a href="/how-it-works">how we work</a>, or <a href="/services">browse our services</a>.</p>',
                    ]],
                    ['type' => 'features', 'data' => [
                        'eyebrow' => 'What We Value',
                        'heading' => 'The principles behind every project',
                        'items' => [
                            ['icon' => 'target', 'title' => 'Transparency', 'text' => 'Fixed prices agreed in writing before we start. What we quote is what you pay — always.'],
                            ['icon' => 'star', 'title' => 'Craftsmanship', 'text' => 'Clean, hand-checked code and considered design. Fast, accessible and built to last.'],
                            ['icon' => 'gauge', 'title' => 'Speed, No Shortcuts', 'text' => 'We move quickly and keep you updated — without skipping the details that matter.'],
                            ['icon' => 'share', 'title' => 'Real Partnership', 'text' => 'You talk to the people building your site, before and after launch. No call-centre scripts.'],
                        ],
                    ]],
                    ['type' => 'stats', 'data' => [
                        'eyebrow' => 'At a Glance',
                        'heading' => 'What working with us looks like',
                        'items' => [
                            ['value' => '50', 'suffix' => '+', 'label' => 'In-house developers'],
                            ['value' => '7', 'suffix' => '-day', 'label' => 'Fastest delivery'],
                            ['value' => '$199', 'suffix' => '', 'label' => 'Starting price'],
                            ['value' => '100', 'suffix' => '%', 'label' => 'Ownership you keep'],
                        ],
                    ]],
                ],
            ],

            // ---------------------------------------------------------------- SERVICES
            [
                'title' => 'Services',
                'slug' => 'services',
                'excerpt' => 'Web design, development, e-commerce, redesigns, on-page SEO and ongoing care — everything to launch and grow online.',
                'seo' => [
                    'title' => 'Web Design, Development & SEO Services',
                    'description' => 'Professional website design, development, e-commerce, redesigns, on-page SEO and ongoing support — built for small businesses at affordable fixed prices.',
                    'focus_keyword' => 'web design services',
                ],
                'sections' => [
                    ['type' => 'richtext', 'data' => [
                        'eyebrow' => 'Our Services',
                        'heading' => 'Websites and web services that grow your business',
                        'width' => 'normal',
                        'body' => '<p>From your first business website to a full e-commerce store or a long-overdue redesign, we offer the services small businesses actually need — nothing padded, nothing you won\'t use. Every engagement is quoted at a clear fixed price, and every website we build is responsive, fast and SEO-ready. Explore each service below, see the <a href="/industries">industries we work with</a>, <a href="/pricing">compare our packages</a>, or <a href="/contact">tell us your goals</a>.</p>',
                    ]],
                    ['type' => 'features', 'data' => [
                        'eyebrow' => 'What We Do',
                        'heading' => 'Services built around your goals',
                        'items' => [
                            ['icon' => 'brush', 'title' => 'Website Design', 'text' => 'Modern, on-brand, mobile-first design that builds trust and turns visitors into enquiries.', 'href' => '/website-design'],
                            ['icon' => 'code', 'title' => 'Website Development', 'text' => 'Fast, clean, standards-based builds with contact and inquiry forms wired up and working.', 'href' => '/website-design'],
                            ['icon' => 'trending', 'title' => 'E-Commerce Websites', 'text' => 'Sell online with a clear product catalogue, secure checkout and a dashboard you can manage.', 'href' => '/ecommerce-website-development'],
                            ['icon' => 'layout', 'title' => 'Website Redesign', 'text' => 'Refresh an outdated or slow site into a modern, high-converting presence — without losing your SEO.', 'href' => '/website-redesign'],
                            ['icon' => 'search', 'title' => 'On-Page & Technical SEO', 'text' => 'Titles, meta tags, clean URLs, schema and fast load times so Google can find and rank you.', 'href' => '/seo-services'],
                            ['icon' => 'shield', 'title' => 'Care & Support', 'text' => 'Updates, fixes, backups and friendly help after launch, so your site stays fast and secure.', 'href' => '/website-maintenance'],
                        ],
                    ]],
                    ['type' => 'pricing', 'data' => [
                        'eyebrow' => 'Packages',
                        'heading' => 'Simple, fixed-price website packages',
                    ]],
                    ['type' => 'faq', 'data' => [
                        'eyebrow' => 'Services FAQ',
                        'heading' => 'Common questions about our services',
                        'items' => [
                            ['question' => 'Which service or package is right for me?', 'answer' => 'If you need a straightforward professional presence, the Starter or Business package covers most small businesses. Choose Premium for a larger content-rich site with a blog, or request a Custom quote for e-commerce and advanced features. Tell us your goals and we\'ll recommend honestly.'],
                            ['question' => 'Can you redesign my existing website?', 'answer' => 'Yes. We can rebuild an outdated, slow or hard-to-edit site into a modern, fast one while preserving your existing URLs and search rankings wherever possible.'],
                            ['question' => 'Do you build online stores?', 'answer' => 'Yes — e-commerce sites are handled as Custom projects so we can scope your products, payments and shipping properly. Request a quote and we\'ll prepare a tailored plan.'],
                            ['question' => 'Is SEO included?', 'answer' => 'On-page SEO basics (titles, meta descriptions, clean URLs, fast performance and structured data) are included with every build. Ongoing SEO campaigns and content can be added on request.'],
                        ],
                    ]],
                ],
            ],

            // ---------------------------------------------------------------- INDUSTRIES
            [
                'title' => 'Industries We Serve',
                'slug' => 'industries',
                'excerpt' => 'Websites tailored to your industry — from restaurants and clinics to trades, startups and online stores, anywhere in the world.',
                'seo' => [
                    'title' => 'Industries We Serve — Website Design for Every Business',
                    'description' => 'Affordable, professional websites tailored to your industry: restaurants, clinics, trades, consultants, startups, online stores, real estate and nonprofits — for businesses worldwide.',
                    'focus_keyword' => 'website design for small business industries',
                ],
                'sections' => [
                    ['type' => 'richtext', 'data' => [
                        'eyebrow' => 'Industries',
                        'heading' => 'Websites built around your industry',
                        'width' => 'normal',
                        'body' => '<p>Every industry sells trust differently. A restaurant needs a mouth-watering menu and easy bookings; a clinic needs reassurance and clear appointment paths; a trade business needs fast quotes and local credibility. We design each website around how <em>your</em> customers actually decide — while keeping the same honest, fixed pricing for everyone. Explore a few of the industries we work with below, then <a href="/contact">tell us about yours</a>.</p>',
                    ]],
                    ['type' => 'features', 'data' => [
                        'eyebrow' => 'Who We Help',
                        'heading' => 'Tailored websites for every kind of business',
                        'items' => [
                            ['icon' => 'star', 'title' => 'Restaurants & Cafés', 'text' => 'Appetising menus, photo galleries, opening hours and one-tap bookings or orders.'],
                            ['icon' => 'shield', 'title' => 'Clinics & Wellness', 'text' => 'Trust-building, accessible sites with clear services and easy appointment requests.'],
                            ['icon' => 'bolt', 'title' => 'Trades & Home Services', 'text' => 'Fast-loading sites with quote forms, service areas and credibility that wins local jobs.'],
                            ['icon' => 'target', 'title' => 'Consultants & Coaches', 'text' => 'Authority-driven personal brands with lead capture, testimonials and clear offers.'],
                            ['icon' => 'rocket', 'title' => 'Startups & SaaS', 'text' => 'Modern, conversion-focused landing pages and marketing sites that scale with you.'],
                            ['icon' => 'trending', 'title' => 'Shops & E-Commerce', 'text' => 'Clean product catalogues, secure checkout and a dashboard you can manage yourself.'],
                            ['icon' => 'map', 'title' => 'Real Estate & Agencies', 'text' => 'Listing-friendly layouts, enquiry forms and polished portfolios that build confidence.'],
                            ['icon' => 'share', 'title' => 'Nonprofits & Community', 'text' => 'Mission-first sites with donation and sign-up journeys that move people to act.'],
                        ],
                    ]],
                    ['type' => 'pricing', 'data' => [
                        'eyebrow' => 'Packages',
                        'heading' => 'One honest price, whatever your industry',
                    ]],
                ],
            ],

            // ---------------------------------------------------------------- HOW IT WORKS
            [
                'title' => 'How It Works',
                'slug' => 'how-it-works',
                'excerpt' => 'A clear four-step process — from your first enquiry to launch and beyond, with a free 15-minute review call.',
                'seo' => [
                    'title' => 'How It Works — Our Website Design Process',
                    'description' => 'See our simple four-step website process: submit requirements, a free 15-minute review call, design and build, then launch and support. Clear timelines and fixed pricing throughout.',
                    'focus_keyword' => 'website design process',
                ],
                'sections' => [
                    ['type' => 'richtext', 'data' => [
                        'eyebrow' => 'How It Works',
                        'heading' => 'A simple, transparent process from start to launch',
                        'width' => 'normal',
                        'body' => '<p>Getting a website shouldn\'t feel like a leap of faith. Our process is deliberately simple and low-pressure: you tell us what you need, we confirm scope and a fixed price on a short call, and only then do we start building. Here\'s exactly what to expect.</p>',
                    ]],
                    ['type' => 'features', 'data' => [
                        'eyebrow' => 'The Process',
                        'heading' => 'From inquiry to launch in four steps',
                        'items' => [
                            ['icon' => 'cursor', 'title' => '1. Submit Requirements', 'text' => 'Pick a package and tell us about your business and goals using our short project form. It takes about two minutes.'],
                            ['icon' => 'phone', 'title' => '2. Free 15-Minute Review', 'text' => 'We review your request and schedule a quick call to confirm scope, answer questions and agree a clear timeline and fixed price.'],
                            ['icon' => 'brush', 'title' => '3. Design & Build', 'text' => 'We design and develop your responsive, SEO-ready website and share it for review, with revisions included in your package.'],
                            ['icon' => 'rocket', 'title' => '4. Launch & Support', 'text' => 'We launch your site, hand over full ownership, and support you after go-live so you\'re never left stuck.'],
                        ],
                    ]],
                    ['type' => 'faq', 'data' => [
                        'eyebrow' => 'Process FAQ',
                        'heading' => 'What to expect along the way',
                        'items' => [
                            ['question' => 'What do you need from me to get started?', 'answer' => 'Your logo and brand colours if you have them, the text and images for your pages (we can guide you), and examples of sites you like. Don\'t have everything ready? We\'ll help you organise it.'],
                            ['question' => 'How many revisions do I get?', 'answer' => 'Each package includes a set number of revision rounds — 2 for Starter, 3 for Business and 5 for Premium — so we can refine the design until it\'s right.'],
                            ['question' => 'How do payments work?', 'answer' => 'Projects are quoted at a fixed price before we begin. Payment terms are agreed on your review call, and prices are one-time (not monthly).'],
                            ['question' => 'What happens after launch?', 'answer' => 'You receive full ownership of your website and files. Business and Premium packages include free technical support (3 and 6 months), and ongoing care can be arranged afterwards.'],
                        ],
                    ]],
                ],
            ],

            // ---------------------------------------------------------------- FAQ
            [
                'title' => 'FAQ',
                'slug' => 'faq',
                'excerpt' => 'Answers to the most common questions about pricing, timelines, ownership, hosting, SEO and support.',
                'seo' => [
                    'title' => 'Frequently Asked Questions About Our Websites',
                    'description' => 'Clear answers on website pricing, delivery times, ownership, hosting and domains, SEO, revisions, payments and support — everything you need before getting started.',
                    'focus_keyword' => 'website design FAQ',
                ],
                'sections' => [
                    ['type' => 'richtext', 'data' => [
                        'eyebrow' => 'FAQ',
                        'heading' => 'Everything you want to know, up front',
                        'width' => 'narrow',
                        'body' => '<p>Honest answers to the questions we hear most. Can\'t find yours? <a href="/contact">Get in touch</a> and we\'ll reply quickly — no pressure, no sales scripts.</p>',
                    ]],
                    ['type' => 'faq', 'data' => [
                        'eyebrow' => 'Answers',
                        'heading' => 'Common questions',
                        'items' => [
                            ['question' => 'How much does a website cost?', 'answer' => 'Our fixed packages are $199 (Starter, 5 pages), $299 (Business, 10 pages) and $599 (Premium, 20 pages). E-commerce and advanced projects are quoted individually. Prices are one-time, in USD, with no monthly fees.'],
                            ['question' => 'How long does it take to build my website?', 'answer' => 'Typical delivery is about 7 days for Starter, 12 days for Business and 3 weeks for Premium, measured from when you provide your content. Timelines are confirmed on your review call.'],
                            ['question' => 'Do I own my website and its content?', 'answer' => 'Yes, completely. When your site launches you own the website, the code and all content. There is no lock-in and no monthly ransom to keep it online.'],
                            ['question' => 'Do you provide hosting and a domain?', 'answer' => 'We focus on design and development rather than reselling hosting, so you keep your website in your own name and host it wherever you prefer. We\'ll guide you through choosing a reliable host and connecting your domain at no extra charge.'],
                            ['question' => 'Will my website work on mobile phones?', 'answer' => 'Always. Every website we build is fully responsive and tested across phones, tablets and desktops, with fast load times and accessible, readable layouts.'],
                            ['question' => 'Is my website good for SEO?', 'answer' => 'Yes. Each build includes on-page SEO fundamentals: clean titles and meta descriptions, logical headings, fast performance, an XML sitemap and structured data, so search engines can crawl and understand your pages.'],
                            ['question' => 'How many changes can I request?', 'answer' => 'Each package includes revision rounds (2 for Starter, 3 for Business, 5 for Premium) so we can refine the design. Additional changes after that can be arranged affordably.'],
                            ['question' => 'What if I need help after launch?', 'answer' => 'Business and Premium packages include 3 and 6 months of free technical support respectively. After that, ongoing care and updates are available whenever you need them.'],
                            ['question' => 'Which countries do you work with?', 'answer' => 'Every country. We are a remote-first studio working with businesses all over the world. All communication is online, prices are shown in USD, and your location is never a barrier.'],
                        ],
                    ]],
                ],
            ],

            // ================================================================ SERVICE DETAIL PAGES
            // ---------------------------------------------------------------- WEBSITE DESIGN
            [
                'title' => 'Website Design Services',
                'slug' => 'website-design',
                'excerpt' => 'Custom, mobile-first website design that builds trust and turns visitors into enquiries — at a clear fixed price.',
                'seo' => [
                    'title' => 'Website Design Services — Affordable & Mobile-First',
                    'description' => 'Professional, custom website design for small businesses worldwide. Mobile-first, fast, conversion-focused and SEO-ready — at honest fixed prices from $199.',
                    'focus_keyword' => 'website design services',
                ],
                'sections' => [
                    ['type' => 'richtext', 'data' => [
                        'eyebrow' => 'Website Design',
                        'heading' => 'Design that earns trust in the first five seconds',
                        'width' => 'normal',
                        'body' => '<p>Your website is often the first impression a customer has of your business — and they decide whether to stay within seconds. We design clean, modern, on-brand websites that load fast, read clearly on any device, and guide visitors towards one obvious next step: getting in touch.</p>'
                            .'<p>Every design is built around <em>your</em> goals rather than a generic template. We keep the process simple and the pricing fixed, so you always know what you\'re getting and what it costs. Prefer to see the numbers first? <a href="/pricing">Compare our packages</a>, learn <a href="/how-it-works">how we work</a>, or <a href="/contact">tell us about your project</a>.</p>',
                    ]],
                    ['type' => 'features', 'data' => [
                        'eyebrow' => 'What\'s Included',
                        'heading' => 'What you get with a website design project',
                        'items' => [
                            ['icon' => 'brush', 'title' => 'Custom, On-Brand Design', 'text' => 'A layout shaped around your brand and audience — not a recycled template everyone else is using.'],
                            ['icon' => 'mobile', 'title' => 'Mobile-First & Responsive', 'text' => 'Designed for phones first, then tablets and desktops, so it looks right on every screen.'],
                            ['icon' => 'target', 'title' => 'Conversion-Focused Layout', 'text' => 'Clear calls to action and a logical journey that turns visitors into enquiries and sales.'],
                            ['icon' => 'gauge', 'title' => 'Fast & Accessible', 'text' => 'Optimised images, lean code and readable, accessible design that respects Core Web Vitals.'],
                            ['icon' => 'search', 'title' => 'SEO-Ready Foundations', 'text' => 'Clean structure, headings, titles and meta tags so search engines can understand your pages.'],
                            ['icon' => 'shield', 'title' => 'Full Ownership', 'text' => 'The finished website and its code are yours to keep and host anywhere — no lock-in.'],
                        ],
                    ]],
                    ['type' => 'faq', 'data' => [
                        'eyebrow' => 'Design FAQ',
                        'heading' => 'Common questions about website design',
                        'items' => [
                            ['question' => 'Do you design from scratch or use templates?', 'answer' => 'We design around your brand and content rather than dropping you into a rigid template. The result is a site that reflects your business and leaves room to grow.'],
                            ['question' => 'Will my website work on phones and tablets?', 'answer' => 'Yes. Every design is mobile-first and tested across phones, tablets and desktops, with fast load times and readable, accessible layouts.'],
                            ['question' => 'How many design revisions are included?', 'answer' => 'Each package includes revision rounds (2 for Starter, 3 for Business, 5 for Premium) so we can refine the design until it feels right.'],
                            ['question' => 'Can you redesign my existing website instead?', 'answer' => 'Absolutely. We also offer a dedicated website redesign service that modernises your current site while protecting your existing URLs and search rankings.'],
                        ],
                    ]],
                    ['type' => 'pricing', 'data' => [
                        'eyebrow' => 'Packages',
                        'heading' => 'Website design at a clear fixed price',
                    ]],
                ],
            ],

            // ---------------------------------------------------------------- E-COMMERCE DEVELOPMENT
            [
                'title' => 'E-Commerce Website Development',
                'slug' => 'ecommerce-website-development',
                'excerpt' => 'Sell online with a fast, secure store — clear product pages, smooth checkout and a dashboard you can run yourself.',
                'seo' => [
                    'title' => 'E-Commerce Website Development — Start Selling Online',
                    'description' => 'Affordable e-commerce website development for small businesses worldwide. Secure checkout, clear product pages, mobile shopping and a dashboard you control.',
                    'focus_keyword' => 'ecommerce website development',
                ],
                'sections' => [
                    ['type' => 'richtext', 'data' => [
                        'eyebrow' => 'E-Commerce',
                        'heading' => 'An online store built to sell, not just to look nice',
                        'width' => 'normal',
                        'body' => '<p>Selling online should feel effortless for your customers and manageable for you. We build e-commerce websites with clear product pages, a frictionless checkout, and a dashboard you can actually run yourself — no developer needed for day-to-day updates.</p>'
                            .'<p>Because every store is different, e-commerce projects are scoped as <a href="/pricing">custom builds</a> so we can plan your products, payments, shipping and tax properly. Tell us what you want to sell and we\'ll prepare a tailored plan — <a href="/contact">request a free quote</a>.</p>',
                    ]],
                    ['type' => 'features', 'data' => [
                        'eyebrow' => 'What\'s Included',
                        'heading' => 'Everything your store needs to take orders',
                        'items' => [
                            ['icon' => 'layout', 'title' => 'Clear Product Catalogue', 'text' => 'Well-structured product and category pages with images, descriptions and prices that convert.'],
                            ['icon' => 'shield', 'title' => 'Secure Checkout', 'text' => 'A smooth, trustworthy checkout with secure, PCI-compliant payment handling.'],
                            ['icon' => 'trending', 'title' => 'Payments & Shipping', 'text' => 'Connect popular payment gateways and set up shipping and tax rules that fit your business.'],
                            ['icon' => 'cursor', 'title' => 'Self-Serve Dashboard', 'text' => 'Add products, update stock and manage orders yourself from an easy admin panel.'],
                            ['icon' => 'mobile', 'title' => 'Built for Mobile Shoppers', 'text' => 'Most shopping happens on phones — your store is fast and effortless to use on any device.'],
                            ['icon' => 'search', 'title' => 'SEO for Products', 'text' => 'Clean URLs, structured data and fast pages so your products can be found on Google.'],
                        ],
                    ]],
                    ['type' => 'faq', 'data' => [
                        'eyebrow' => 'E-Commerce FAQ',
                        'heading' => 'Common questions about online stores',
                        'items' => [
                            ['question' => 'How much does an online store cost?', 'answer' => 'E-commerce sites are quoted individually because scope varies a lot — number of products, payment methods, shipping rules and integrations. We agree a clear fixed price before any work begins.'],
                            ['question' => 'Which payment methods can you support?', 'answer' => 'We can integrate widely used, secure payment gateways so you can accept cards and popular wallets. We\'ll recommend the best fit for your country and customers.'],
                            ['question' => 'Can I manage products myself afterwards?', 'answer' => 'Yes. You get an easy dashboard to add products, edit prices, manage stock and process orders without needing a developer.'],
                            ['question' => 'Will my store work internationally?', 'answer' => 'Yes — we build for a global audience, with fast performance worldwide and mobile-first design. We can set up the currencies, shipping and tax rules your markets need.'],
                        ],
                    ]],
                    ['type' => 'pricing', 'data' => [
                        'eyebrow' => 'Packages',
                        'heading' => 'Start with a package or request a custom store',
                    ]],
                ],
            ],

            // ---------------------------------------------------------------- WEBSITE REDESIGN
            [
                'title' => 'Website Redesign Services',
                'slug' => 'website-redesign',
                'excerpt' => 'Turn an outdated, slow or hard-to-edit website into a modern, fast, high-converting one — without losing your SEO.',
                'seo' => [
                    'title' => 'Website Redesign Services — Modernise Without Losing SEO',
                    'description' => 'Redesign your outdated website into a fast, modern, mobile-friendly site that converts — while preserving your existing URLs and search rankings. Fixed pricing.',
                    'focus_keyword' => 'website redesign services',
                ],
                'sections' => [
                    ['type' => 'richtext', 'data' => [
                        'eyebrow' => 'Website Redesign',
                        'heading' => 'A modern website — without losing what already works',
                        'width' => 'normal',
                        'body' => '<p>If your current website looks dated, loads slowly, is painful to edit, or simply isn\'t bringing in enquiries, a redesign can transform it. The worry we hear most is "will I lose my Google rankings?" — and the answer, done properly, is no.</p>'
                            .'<p>We rebuild your site to be fast, mobile-friendly and conversion-focused while carefully preserving your existing URLs (or mapping them with 301 redirects) so your hard-won SEO carries over. <a href="/contact">Share your current site</a> and we\'ll tell you honestly what\'s worth keeping, what to improve, and what it will cost.</p>',
                    ]],
                    ['type' => 'features', 'data' => [
                        'eyebrow' => 'What\'s Included',
                        'heading' => 'What a redesign gives you',
                        'items' => [
                            ['icon' => 'brush', 'title' => 'A Modern, Fresh Design', 'text' => 'A contemporary, on-brand look that builds trust and reflects where your business is today.'],
                            ['icon' => 'search', 'title' => 'SEO Preserved', 'text' => 'We keep your URLs where possible and set up 301 redirects where not, so rankings and traffic carry over.'],
                            ['icon' => 'gauge', 'title' => 'Much Faster Performance', 'text' => 'Lean, modern code and optimised images fix the slow load times that cost you visitors.'],
                            ['icon' => 'mobile', 'title' => 'Fully Mobile-Friendly', 'text' => 'A responsive rebuild so your site finally works properly for the majority on phones.'],
                            ['icon' => 'target', 'title' => 'Clearer Conversions', 'text' => 'A rethought layout and calls to action that turn more of your existing traffic into enquiries.'],
                            ['icon' => 'layout', 'title' => 'Content Carried Over', 'text' => 'We migrate your existing content and improve it, so nothing important gets left behind.'],
                        ],
                    ]],
                    ['type' => 'faq', 'data' => [
                        'eyebrow' => 'Redesign FAQ',
                        'heading' => 'Common questions about redesigns',
                        'items' => [
                            ['question' => 'Will I lose my Google rankings if I redesign?', 'answer' => 'Not when it\'s handled carefully. We preserve your existing URL structure where possible and add 301 redirects where pages move, so search engines pass your authority to the new pages.'],
                            ['question' => 'Can you keep my existing content and images?', 'answer' => 'Yes. We migrate your current content and assets, and improve the structure, copy and imagery where it helps.'],
                            ['question' => 'My site is on another platform — can you still help?', 'answer' => 'In most cases, yes. Tell us where your site is hosted and built, and we\'ll confirm what\'s possible on your review call.'],
                            ['question' => 'How long does a redesign take?', 'answer' => 'Similar to a new build — roughly 1 to 3 weeks depending on size — assuming access and content are ready. The stages mirror our standard four-step process.'],
                        ],
                    ]],
                    ['type' => 'pricing', 'data' => [
                        'eyebrow' => 'Packages',
                        'heading' => 'Redesign at a clear fixed price',
                    ]],
                ],
            ],

            // ---------------------------------------------------------------- WEBSITE MAINTENANCE
            [
                'title' => 'Website Maintenance & Support',
                'slug' => 'website-maintenance',
                'excerpt' => 'Keep your website fast, secure and up to date with friendly, on-demand maintenance and support — no lock-in.',
                'seo' => [
                    'title' => 'Website Maintenance & Support Services',
                    'description' => 'Affordable website maintenance and support: updates, security patches, backups, fixes and content changes that keep your site fast, secure and working. No lock-in.',
                    'focus_keyword' => 'website maintenance services',
                ],
                'sections' => [
                    ['type' => 'richtext', 'data' => [
                        'eyebrow' => 'Care & Support',
                        'heading' => 'Keep your website healthy after launch',
                        'width' => 'normal',
                        'body' => '<p>A website isn\'t "finished" at launch — it needs occasional care to stay fast, secure and current. Whether you want hands-off peace of mind or just someone reliable to call when something needs changing, we keep your site in good shape so you can focus on your business.</p>'
                            .'<p>Business and Premium packages include free technical support (3 and 6 months), and ongoing care can be arranged affordably after that — with no long lock-in contracts. <a href="/contact">Tell us what you need</a> and we\'ll suggest the right level of support.</p>',
                    ]],
                    ['type' => 'features', 'data' => [
                        'eyebrow' => 'What\'s Included',
                        'heading' => 'What ongoing maintenance covers',
                        'items' => [
                            ['icon' => 'shield', 'title' => 'Updates & Security', 'text' => 'Keep software and components patched so your site stays secure and reliable.'],
                            ['icon' => 'clock', 'title' => 'Regular Backups', 'text' => 'Scheduled backups so your site can be restored quickly if anything ever goes wrong.'],
                            ['icon' => 'bolt', 'title' => 'Fixes & Small Tweaks', 'text' => 'Quick fixes, edits and small improvements handled without the fuss.'],
                            ['icon' => 'gauge', 'title' => 'Speed & Uptime Checks', 'text' => 'Periodic performance and availability checks to catch issues before your visitors do.'],
                            ['icon' => 'layout', 'title' => 'Content Updates', 'text' => 'New pages, offers, images or text updated for you whenever your business changes.'],
                            ['icon' => 'phone', 'title' => 'Friendly Support', 'text' => 'A real person to talk to — no call-centre scripts, no being left stuck.'],
                        ],
                    ]],
                    ['type' => 'faq', 'data' => [
                        'eyebrow' => 'Support FAQ',
                        'heading' => 'Common questions about maintenance',
                        'items' => [
                            ['question' => 'Do I have to sign a long contract?', 'answer' => 'No. We keep things flexible — use support when you need it. Business and Premium packages include a free support period, and ongoing care is available without long lock-in.'],
                            ['question' => 'What if I only need occasional changes?', 'answer' => 'That\'s fine. You can reach out as and when you need an update or fix, and we\'ll quote small jobs fairly.'],
                            ['question' => 'Can you maintain a site you didn\'t build?', 'answer' => 'Often yes. Share details of your current website and we\'ll let you know what we can support.'],
                            ['question' => 'Does maintenance include hosting?', 'answer' => 'We focus on care and updates rather than reselling hosting, so your site stays in your own name. We\'re happy to help you with your host when needed.'],
                        ],
                    ]],
                ],
            ],

            // ---------------------------------------------------------------- SEO SERVICES
            [
                'title' => 'On-Page & Technical SEO Services',
                'slug' => 'seo-services',
                'excerpt' => 'The on-page and technical SEO foundations that help your website get found on Google — set up properly from day one.',
                'seo' => [
                    'title' => 'On-Page & Technical SEO Services for Small Business',
                    'description' => 'On-page and technical SEO for small-business websites: titles, meta, clean structure, schema, speed and sitemaps — the foundations that help Google find and understand you.',
                    'focus_keyword' => 'on-page SEO services',
                ],
                'sections' => [
                    ['type' => 'richtext', 'data' => [
                        'eyebrow' => 'SEO',
                        'heading' => 'Get the SEO foundations right from the start',
                        'width' => 'normal',
                        'body' => '<p>Great SEO starts with the basics done well. Before you spend on ads or link-building, your website needs a solid on-page and technical foundation so search engines can crawl, understand and index your pages. We build that foundation into every site — and can set it up on an existing one too.</p>'
                            .'<p>We\'re honest about what SEO can and can\'t do: no one can guarantee a #1 ranking, and anyone who promises that is best avoided. What we <em>can</em> do is remove the technical barriers and give your genuinely helpful content the best chance to rank over time. New to this? Start with our guides on <a href="/blog/7-on-page-seo-basics-every-new-website-needs">on-page SEO basics</a> and <a href="/blog/local-seo-basics-how-small-businesses-show-up-on-google-maps">local SEO</a>, or <a href="/contact">ask us about your site</a>.</p>',
                    ]],
                    ['type' => 'features', 'data' => [
                        'eyebrow' => 'What\'s Included',
                        'heading' => 'The SEO foundations we set up',
                        'items' => [
                            ['icon' => 'search', 'title' => 'Keyword-Aware Content', 'text' => 'Pages structured around what your customers actually search for — written for people first.'],
                            ['icon' => 'code', 'title' => 'Titles & Meta Tags', 'text' => 'Unique, compelling title tags and meta descriptions on every page to improve click-through.'],
                            ['icon' => 'layout', 'title' => 'Clean URLs & Structure', 'text' => 'Logical, readable URLs and a clear heading hierarchy that search engines can follow.'],
                            ['icon' => 'star', 'title' => 'Structured Data (Schema)', 'text' => 'Organization, breadcrumb, article and FAQ schema so search engines understand your content.'],
                            ['icon' => 'gauge', 'title' => 'Speed & Core Web Vitals', 'text' => 'Fast-loading, stable pages — a real ranking and conversion factor, not an afterthought.'],
                            ['icon' => 'map', 'title' => 'Sitemap & Indexing', 'text' => 'An XML sitemap, robots.txt and canonical tags so your pages get discovered and indexed.'],
                        ],
                    ]],
                    ['type' => 'faq', 'data' => [
                        'eyebrow' => 'SEO FAQ',
                        'heading' => 'Common questions about SEO',
                        'items' => [
                            ['question' => 'Can you guarantee a #1 ranking on Google?', 'answer' => 'No — and you should be cautious of anyone who does. Rankings depend on your content, competition and many factors outside any agency\'s control. We build the technical foundation and helpful content that give you the best possible chance to rank over time.'],
                            ['question' => 'Is SEO included when you build my website?', 'answer' => 'Yes. On-page and technical SEO fundamentals — titles, meta, clean structure, schema, speed and a sitemap — are included with every build at no extra cost.'],
                            ['question' => 'Do you offer ongoing SEO campaigns?', 'answer' => 'The foundational setup is included; ongoing content and SEO work (such as new articles and continued optimisation) can be arranged on request. Tell us your goals and we\'ll suggest a sensible, honest plan.'],
                            ['question' => 'How long does SEO take to work?', 'answer' => 'SEO is a long game. Technical fixes can help quickly, but ranking for competitive terms usually takes months of consistent, helpful content. We\'ll set realistic expectations up front.'],
                        ],
                    ]],
                ],
            ],

            // ---------------------------------------------------------------- PORTFOLIO / OUR WORK
            [
                'title' => 'Our Work',
                'slug' => 'portfolio',
                'excerpt' => 'The range of websites we design and build — and exactly what goes into every project.',
                'seo' => [
                    'title' => 'Our Work — Website Design Portfolio & Examples',
                    'description' => 'See the kind of websites we design and build — business sites, online stores, redesigns, booking and landing pages — and what goes into every project. Fixed pricing, full ownership.',
                    'focus_keyword' => 'web design portfolio',
                ],
                'sections' => [
                    ['type' => 'richtext', 'data' => [
                        'eyebrow' => 'Our Work',
                        'heading' => 'Custom websites, built around real business goals',
                        'width' => 'normal',
                        'body' => '<p>We don\'t do cookie-cutter. Every website we build is designed around a specific business, its customers and the action it needs visitors to take — whether that\'s a booking, an order or an enquiry. Below is the range of work we take on and exactly what goes into each build.</p>'
                            .'<p>We\'re growing this showcase as we go, and we\'re always happy to walk you through examples relevant to your industry — just <a href="/contact">ask us</a>. Prefer to talk specifics? <a href="/services">Explore our services</a>, <a href="/pricing">compare packages</a>, or see the <a href="/industries">industries we work with</a>.</p>',
                    ]],
                    ['type' => 'features', 'data' => [
                        'eyebrow' => 'What We Build',
                        'heading' => 'The kind of websites we design and build',
                        'items' => [
                            ['icon' => 'brush', 'title' => 'Business & Brochure Sites', 'text' => 'Professional multi-page sites that present your services clearly and win trust.', 'href' => '/website-design'],
                            ['icon' => 'trending', 'title' => 'Online Stores', 'text' => 'E-commerce sites with clear product pages, secure checkout and easy management.', 'href' => '/ecommerce-website-development'],
                            ['icon' => 'phone', 'title' => 'Booking & Service Sites', 'text' => 'Lead- and booking-focused sites for clinics, trades and local service businesses.', 'href' => '/industries'],
                            ['icon' => 'layout', 'title' => 'Website Redesigns', 'text' => 'Modern rebuilds of dated or slow sites that keep your existing SEO intact.', 'href' => '/website-redesign'],
                            ['icon' => 'target', 'title' => 'Landing Pages & Microsites', 'text' => 'Focused, high-converting pages for a campaign, product or single offer.', 'href' => '/services'],
                            ['icon' => 'search', 'title' => 'Blogs & Content Hubs', 'text' => 'Content-rich sites built to publish regularly and grow organic search traffic.', 'href' => '/seo-services'],
                        ],
                    ]],
                    ['type' => 'features', 'data' => [
                        'eyebrow' => 'Built In, Every Time',
                        'heading' => 'What every project includes as standard',
                        'items' => [
                            ['icon' => 'mobile', 'title' => 'Mobile-First & Responsive', 'text' => 'Tested across phones, tablets and desktops so it looks right everywhere.'],
                            ['icon' => 'gauge', 'title' => 'Fast Performance', 'text' => 'Optimised images and lean code with Core Web Vitals in mind.'],
                            ['icon' => 'search', 'title' => 'SEO Foundations', 'text' => 'Clean structure, titles, meta, schema and a sitemap so Google understands your site.'],
                            ['icon' => 'check', 'title' => 'Accessible & Readable', 'text' => 'Considered contrast, clear type and sensible structure for every visitor.'],
                            ['icon' => 'shield', 'title' => 'Secure & Reliable', 'text' => 'Built on solid, standards-based code with security basics handled.'],
                            ['icon' => 'star', 'title' => 'Yours to Own', 'text' => 'You keep the website, code and content outright — no monthly lock-in.'],
                        ],
                    ]],
                    ['type' => 'testimonials', 'data' => [
                        'eyebrow' => 'Client Stories',
                        'heading' => 'What our clients say',
                        'items' => [],
                    ]],
                    ['type' => 'pricing', 'data' => [
                        'eyebrow' => 'Packages',
                        'heading' => 'Start your project at a clear fixed price',
                    ]],
                ],
            ],

            // ---------------------------------------------------------------- PRICING (dedicated template)
            [
                'title' => 'Pricing',
                'slug' => 'pricing',
                'excerpt' => 'Transparent, affordable website packages from $199. Compare Starter, Business, Premium and Custom plans.',
                'seo' => [
                    'title' => 'Website Pricing & Packages from $199',
                    'description' => 'Affordable, transparent website packages starting at $199 one-time. Compare Starter, Business, Premium and Custom plans — fixed pricing, no monthly lock-in, full ownership.',
                    'focus_keyword' => 'website design packages pricing',
                ],
                'sections' => [],
            ],

            // ---------------------------------------------------------------- BLOG (dedicated template)
            [
                'title' => 'Blog',
                'slug' => 'blog',
                'excerpt' => 'Practical, jargon-free articles on web design, SEO and growing your business online.',
                'seo' => [
                    'title' => 'Blog — Web Design & Online Growth Tips',
                    'description' => 'Practical, honest articles on web design, website pricing, on-page SEO and converting visitors — written to help small businesses grow online.',
                    'focus_keyword' => 'web design blog',
                ],
                'sections' => [],
            ],

            // ---------------------------------------------------------------- CONTACT (dedicated template)
            [
                'title' => 'Contact Us',
                'slug' => 'contact',
                'excerpt' => 'Tell us about your project and get a free quote plus a 15-minute review meeting.',
                'seo' => [
                    'title' => 'Contact Us — Get Your Website Quote',
                    'description' => 'Get an affordable professional website. Share your project requirements and we\'ll reply quickly with a free quote and a 15-minute review meeting.',
                    'focus_keyword' => 'get a website quote',
                ],
                'sections' => [],
            ],

            // ---------------------------------------------------------------- PRIVACY
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy',
                'excerpt' => 'How we collect, use and protect your personal information.',
                'seo' => [
                    'title' => 'Privacy Policy',
                    'description' => 'How we collect, use, store and protect the personal information you share with us, and the choices you have over your data.',
                    'robots' => 'index, follow',
                ],
                'sections' => [
                    ['type' => 'richtext', 'data' => [
                        'eyebrow' => 'Legal',
                        'heading' => 'Privacy Policy',
                        'width' => 'narrow',
                        'body' => '<p><em>Last updated: '.now()->format('F Y').'. This is a general template. Please review it with a qualified professional and tailor it to your business and jurisdiction before publishing.</em></p>'
                            .'<p>This Privacy Policy explains how we collect, use and protect the information you provide when you visit our website or request a quote. By using this website you agree to the practices described here.</p>'
                            .'<h3>Information we collect</h3>'
                            .'<ul><li><strong>Information you give us</strong> — such as your name, email address, phone number and project details when you submit an inquiry or contact form.</li>'
                            .'<li><strong>Usage information</strong> — such as pages visited, approximate location and device/browser type, collected through cookies and analytics to help us improve the site.</li></ul>'
                            .'<h3>How we use your information</h3>'
                            .'<ul><li>To respond to your inquiry and prepare a quote or proposal.</li>'
                            .'<li>To deliver and support the services you request.</li>'
                            .'<li>To improve our website, content and user experience.</li>'
                            .'<li>To send service-related communications. We do not sell your personal data.</li></ul>'
                            .'<h3>Cookies</h3>'
                            .'<p>We use essential cookies to make the site work and, where enabled, analytics cookies to understand how visitors use the site. You can control cookies through your browser settings.</p>'
                            .'<h3>Data retention and security</h3>'
                            .'<p>We keep personal information only as long as needed for the purposes above or as required by law, and we apply reasonable technical and organisational measures to protect it.</p>'
                            .'<h3>Your rights</h3>'
                            .'<p>Depending on where you live, you may have the right to access, correct or delete your personal data, or object to certain processing. To exercise these rights, <a href="/contact">contact us</a>.</p>'
                            .'<h3>Contact</h3>'
                            .'<p>Questions about this policy? Please <a href="/contact">get in touch</a> and we\'ll be happy to help.</p>',
                    ]],
                ],
            ],

            // ---------------------------------------------------------------- TERMS
            [
                'title' => 'Terms of Service',
                'slug' => 'terms',
                'excerpt' => 'The terms that govern the use of our website and services.',
                'seo' => [
                    'title' => 'Terms of Service',
                    'description' => 'The terms and conditions that govern your use of our website and the design and development services we provide.',
                    'robots' => 'index, follow',
                ],
                'sections' => [
                    ['type' => 'richtext', 'data' => [
                        'eyebrow' => 'Legal',
                        'heading' => 'Terms of Service',
                        'width' => 'narrow',
                        'body' => '<p><em>Last updated: '.now()->format('F Y').'. This is a general template. Please review it with a qualified professional and tailor it to your business before publishing.</em></p>'
                            .'<p>These Terms of Service govern your use of this website and the services we provide. By engaging our services or using this website, you agree to these terms.</p>'
                            .'<h3>Our services</h3>'
                            .'<p>We provide website design and development services as described in the package or proposal agreed with you. The specific scope, deliverables, timeline and price for your project are confirmed in writing before work begins.</p>'
                            .'<h3>Quotes and payments</h3>'
                            .'<ul><li>Prices are quoted in USD and are one-time unless stated otherwise.</li>'
                            .'<li>Payment terms are agreed before the project starts.</li>'
                            .'<li>Prices shown on this website are indicative and may change for custom scopes.</li></ul>'
                            .'<h3>Client responsibilities</h3>'
                            .'<p>You agree to provide the content, materials and feedback needed to complete your project in a timely manner, and confirm you have the right to use any materials you supply.</p>'
                            .'<h3>Revisions and delivery</h3>'
                            .'<p>Each package includes a defined number of revision rounds. Delivery timelines assume content and feedback are provided promptly. Additional work beyond the agreed scope may be quoted separately.</p>'
                            .'<h3>Ownership</h3>'
                            .'<p>On full payment, you own the final website and its content. Any third-party components remain subject to their own licences.</p>'
                            .'<h3>Limitation of liability</h3>'
                            .'<p>Our services are provided with reasonable care and skill. To the extent permitted by law, we are not liable for indirect or consequential losses arising from the use of your website.</p>'
                            .'<h3>Contact</h3>'
                            .'<p>Questions about these terms? Please <a href="/contact">contact us</a>.</p>',
                    ]],
                ],
            ],
        ];
    }
}
