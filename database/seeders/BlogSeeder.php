<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::whereNotNull('role_id')->first();

        $categories = collect([
            'Web Design' => 'Design, UX and build advice for business websites.',
            'SEO' => 'Practical search-engine optimisation for small businesses.',
            'Business Growth' => 'Pricing, planning and growing your business online.',
            'E-Commerce' => 'Selling online, stores and online payments.',
            'Marketing' => 'Content, lead generation and growing your audience.',
        ])->mapWithKeys(fn ($desc, $name) => [$name => Category::updateOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name, 'description' => $desc],
        )]);

        $tags = collect(['affordable', 'small-business', 'seo', 'conversion', 'international', 'pricing', 'website-cost', 'process', 'maintenance', 'redesign', 'local-seo', 'ecommerce', 'domain', 'hosting', 'content', 'speed', 'security', 'accessibility', 'mobile', 'branding', 'leads'])
            ->mapWithKeys(fn ($t) => [$t => Tag::updateOrCreate(['slug' => $t], ['name' => Str::headline($t)])]);

        foreach ($this->posts() as $i => $data) {
            $post = Post::updateOrCreate(['slug' => Str::slug($data['title'])], [
                'title' => $data['title'],
                'excerpt' => $data['excerpt'],
                'body' => $data['body'],
                'author_id' => $author?->id,
                'category_id' => $categories[$data['category']]->id,
                'status' => 'published',
                'published_at' => now()->subDays(($i + 1) * 4),
            ]);

            $post->tags()->sync(collect($data['tags'])->map(fn ($t) => $tags[$t]->id)->all());

            // Brand-free SEO title (SeoService appends the site name automatically).
            $post->seo()->updateOrCreate([], [
                'title' => $data['seo_title'] ?? $data['title'],
                'description' => $data['excerpt'],
            ]);
        }
    }

    private function posts(): array
    {
        return [
            [
                'title' => 'How Much Should a Small Business Website Cost in 2026?',
                'category' => 'Business Growth',
                'tags' => ['affordable', 'small-business', 'pricing', 'website-cost'],
                'seo_title' => 'How Much Should a Small Business Website Cost in 2026?',
                'excerpt' => 'A clear, honest breakdown of what a professional small-business website really costs in 2026 — and how to avoid overpaying or getting burned by cheap deals.',
                'body' => '<p>"How much does a website cost?" is the first question nearly every business owner asks — and the answer is frustratingly vague online. Prices range from free to tens of thousands of dollars. This guide cuts through the noise with realistic 2026 figures and the factors that actually move the price.</p>'
                    .'<h2>The honest 2026 price ranges</h2>'
                    .'<p>Based on current industry data, here is what a professional small-business website typically costs:</p>'
                    .'<ul><li><strong>DIY website builders:</strong> roughly $0–$300 upfront plus a monthly subscription. Cheapest to start, but the design is templated and you do all the work.</li>'
                    .'<li><strong>Freelancers:</strong> around $500–$5,000 depending on experience and scope.</li>'
                    .'<li><strong>Agencies (USA):</strong> commonly $2,000–$10,000 for a standard 5–10 page site, and far more for custom builds.</li>'
                    .'<li><strong>Agencies (UK):</strong> commonly £1,500–£5,000 for most small businesses.</li></ul>'
                    .'<p>For context, our own <a href="/pricing">fixed packages</a> start at $199 for a 5-page site and $599 for a 20-page site — deliberately below typical agency rates, because the process is streamlined and you own everything.</p>'
                    .'<h2>What actually drives the price</h2>'
                    .'<h3>1. Number of pages and content</h3>'
                    .'<p>A 5-page brochure site is far quicker to build than a 20-page site with a blog. More pages means more design, copy and testing.</p>'
                    .'<h3>2. Custom design vs template</h3>'
                    .'<p>A bespoke, on-brand design costs more than a lightly customised template — but it builds more trust and stands out.</p>'
                    .'<h3>3. Functionality</h3>'
                    .'<p>Contact forms are standard. Online stores, bookings, memberships and integrations add real development time, which is why e-commerce is usually quoted as a <a href="/pricing">custom project</a>.</p>'
                    .'<h3>4. Ongoing costs</h3>'
                    .'<p>Budget for hosting, a domain and occasional maintenance. These are usually modest, but watch for "cheap" monthly website deals that lock you in — over five years they can cost several times the sticker price.</p>'
                    .'<h2>Red flags to avoid</h2>'
                    .'<ul><li>Prices with no written scope.</li><li>You don\'t own the finished website or its code.</li><li>Mandatory monthly fees just to keep the site online.</li><li>No clear timeline or revision policy.</li></ul>'
                    .'<h2>So what should you budget?</h2>'
                    .'<p>For most small businesses, a professional, SEO-ready, mobile-friendly website is achievable for a few hundred dollars with a fixed-price provider, or a few thousand with a traditional agency. The key is transparency: know the scope, the timeline, the ownership terms and the total cost before you commit. See our <a href="/faq">FAQ</a> for more, or <a href="/contact">request a free quote</a>.</p>',
            ],
            [
                'title' => '7 On-Page SEO Basics Every New Website Needs',
                'category' => 'SEO',
                'tags' => ['seo', 'small-business', 'conversion'],
                'seo_title' => '7 On-Page SEO Basics Every New Website Needs',
                'excerpt' => 'Simple, high-impact on-page SEO steps that help your new website get found on Google from day one — no technical background required.',
                'body' => '<p>You don\'t need to be an SEO expert to give your new website a strong start on Google. Most of the value comes from a handful of on-page fundamentals. Here are the seven that matter most — all of which we set up on <a href="/services">every website we build</a>.</p>'
                    .'<h2>1. A clear title tag on every page</h2>'
                    .'<p>The title tag is the clickable headline in search results. Make each one unique, under ~60 characters, and lead with what the page is about (for example, "Affordable Website Design for Cafés — [City]").</p>'
                    .'<h2>2. A compelling meta description</h2>'
                    .'<p>This is the short summary under your title in search. It doesn\'t directly affect rankings, but a clear, benefit-led description improves click-through. Keep it under ~155 characters.</p>'
                    .'<h2>3. One H1 and a logical heading structure</h2>'
                    .'<p>Use a single H1 for the page\'s main topic, then H2s and H3s for sections. This helps both readers and search engines understand your content.</p>'
                    .'<h2>4. Readable, keyword-aware URLs</h2>'
                    .'<p>Short, descriptive URLs (like <code>/services</code>, not <code>/page?id=12</code>) are easier to share and rank. Avoid changing URLs later without a 301 redirect.</p>'
                    .'<h2>5. Fast load times and mobile-friendliness</h2>'
                    .'<p>Google ranks with Core Web Vitals and mobile usability in mind. Compress images, keep code lean, and test on a phone. Speed is also one of the biggest drivers of conversions.</p>'
                    .'<h2>6. Descriptive image ALT text</h2>'
                    .'<p>Add concise ALT text describing each meaningful image. It improves accessibility and helps your images appear in Google Image search.</p>'
                    .'<h2>7. Internal links and structured data</h2>'
                    .'<p>Link related pages to each other (for example, link your services to your <a href="/pricing">pricing</a>), and add structured data (schema) so search engines understand your business, articles and FAQs.</p>'
                    .'<h2>Start as you mean to go on</h2>'
                    .'<p>Getting these basics right from launch saves painful fixes later. Pair them with genuinely helpful content and you\'ve got a foundation that compounds over time. Want them handled for you? <a href="/contact">Get in touch</a> — on-page SEO basics are included with every build.</p>',
            ],
            [
                'title' => 'Designing Websites That Convert International Visitors',
                'category' => 'Web Design',
                'tags' => ['conversion', 'international', 'small-business'],
                'seo_title' => 'Designing Websites That Convert International Visitors',
                'excerpt' => 'Design and trust signals that turn visitors from anywhere in the world into paying customers.',
                'body' => '<p>Attracting visitors from different countries is one thing; convincing them to buy or enquire is another. If you serve customers around the world — across different time zones, languages and currencies — a few deliberate design choices make a big difference to conversion.</p>'
                    .'<h2>1. Build trust in the first five seconds</h2>'
                    .'<p>International visitors can\'t walk into your shop, so your website has to earn trust fast. Use a clean, professional design, real photos where possible, clear contact details, and visible policies like <a href="/privacy">privacy</a> and <a href="/terms">terms</a>.</p>'
                    .'<h2>2. Speak to each audience clearly</h2>'
                    .'<p>Use plain, universal English and avoid region-specific slang. Where it matters, show pricing in a clear currency (we quote in USD) and state which countries you serve so visitors immediately know they\'re in the right place.</p>'
                    .'<h2>3. Make speed a priority</h2>'
                    .'<p>Visitors far from your server feel every extra second. Optimised images, lean code and good hosting keep load times low worldwide — which protects both rankings and conversions.</p>'
                    .'<h2>4. Design for mobile first</h2>'
                    .'<p>In many markets most traffic is mobile. Large tap targets, readable type and fast-loading pages aren\'t optional — they\'re the default experience most people will have.</p>'
                    .'<h2>5. Use clear, repeated calls to action</h2>'
                    .'<p>Don\'t make visitors hunt for the next step. A prominent, consistent call to action — "Get a free quote", "View packages" — on every page removes friction. Keep forms short and ask only for what you need.</p>'
                    .'<h2>6. Reduce perceived risk</h2>'
                    .'<p>Transparent pricing, clear timelines, and honest FAQs lower the fear of the unknown. When buyers can\'t meet you in person, removing doubt is half the sale. (It\'s exactly why our <a href="/pricing">pricing</a> is fixed and public.)</p>'
                    .'<h2>Bring it together</h2>'
                    .'<p>Conversion isn\'t about tricks — it\'s about clarity, speed and trust applied consistently. Get those right and visitors from anywhere in the world feel confident choosing you. Ready to build one? <a href="/contact">Tell us about your project</a>.</p>',
            ],
            [
                'title' => 'Website Builder vs Web Design Agency: Which Is Right for Your Small Business?',
                'category' => 'Business Growth',
                'tags' => ['small-business', 'affordable', 'pricing'],
                'seo_title' => 'Website Builder vs Web Design Agency: Which Is Right?',
                'excerpt' => 'A balanced look at DIY website builders versus hiring a professional — the real pros, cons and costs, so you can choose with confidence.',
                'body' => '<p>When you need a website, the first decision is how to build it: do it yourself with a website builder, or hire a professional. Both are valid — the right answer depends on your time, budget and how important the site is to your business. Here\'s an honest comparison.</p>'
                    .'<h2>Website builders: fast, cheap, and all on you</h2>'
                    .'<p><strong>Best for:</strong> very tight budgets, simple needs, and people who enjoy doing it themselves.</p>'
                    .'<ul><li><strong>Pros:</strong> low upfront cost, quick to start, no technical partner needed.</li>'
                    .'<li><strong>Cons:</strong> templated designs that can look generic, limited room to grow, performance and SEO constraints, and the time cost of building and maintaining it yourself.</li></ul>'
                    .'<h2>A professional (freelancer or agency): done for you, built to grow</h2>'
                    .'<p><strong>Best for:</strong> businesses where the website drives leads or sales, or that need custom branding and room to expand.</p>'
                    .'<ul><li><strong>Pros:</strong> custom, on-brand design; better speed and SEO foundations; features added as you grow; and your time stays on your business.</li>'
                    .'<li><strong>Cons:</strong> higher cost than DIY, and quality depends on choosing a reliable provider.</li></ul>'
                    .'<h2>The rule of thumb</h2>'
                    .'<p>If your website is mainly a placeholder, a builder may be fine. If it\'s a primary source of enquiries or revenue — or it represents your brand to customers who\'ve never met you — invest in a professional build. The design, speed and credibility usually pay for themselves.</p>'
                    .'<h2>The middle path: fixed-price professional websites</h2>'
                    .'<p>The old trade-off was "cheap and DIY" versus "expensive and done-for-you". Fixed-price providers sit in between: a professionally built, owned-by-you website at a predictable price. That\'s the model behind our <a href="/pricing">packages</a> — professional results without agency-level invoices.</p>'
                    .'<p>Still weighing it up? Our guide to <a href="/blog/how-much-should-a-small-business-website-cost-in-2026">what a website should cost</a> breaks down the numbers, or <a href="/contact">ask us</a> for honest advice on what you actually need.</p>',
            ],
            [
                'title' => 'How Long Does It Take to Build a Website? A Realistic Timeline',
                'category' => 'Web Design',
                'tags' => ['process', 'small-business', 'conversion'],
                'seo_title' => 'How Long Does It Take to Build a Website? A Realistic Timeline',
                'excerpt' => 'A realistic look at website build timelines — the phases involved, what slows projects down, and how to launch faster without cutting corners.',
                'body' => '<p>"How long will my website take?" is a fair question with an honest answer: it depends mostly on the size of the site and how quickly content and feedback come back. Here\'s a realistic timeline and how to keep your project moving.</p>'
                    .'<h2>Typical timelines by site size</h2>'
                    .'<ul><li><strong>Small site (around 5 pages):</strong> roughly 1 week.</li>'
                    .'<li><strong>Business site (around 10 pages):</strong> roughly 2 weeks.</li>'
                    .'<li><strong>Larger site (around 20 pages, with a blog):</strong> roughly 3 weeks.</li>'
                    .'<li><strong>E-commerce / custom builds:</strong> several weeks, scoped individually.</li></ul>'
                    .'<p>These assume content is ready and feedback is prompt. You can see the exact stages on our <a href="/how-it-works">how it works</a> page.</p>'
                    .'<h2>The phases of a build</h2>'
                    .'<h3>1. Discovery and scope</h3>'
                    .'<p>A short call to confirm goals, pages, and a fixed price. Getting this right prevents delays later.</p>'
                    .'<h3>2. Design</h3>'
                    .'<p>Layouts and styling that match your brand, shared for review.</p>'
                    .'<h3>3. Development</h3>'
                    .'<p>Turning the design into a fast, responsive, SEO-ready website with your content and forms.</p>'
                    .'<h3>4. Review and revisions</h3>'
                    .'<p>You review; we refine within the revision rounds included in your package.</p>'
                    .'<h3>5. Launch</h3>'
                    .'<p>Final checks, then go-live — and full ownership handed to you.</p>'
                    .'<h2>What slows projects down</h2>'
                    .'<ul><li>Waiting on text, images or logos.</li><li>Slow or scattered feedback.</li><li>Scope growing mid-project without a plan.</li></ul>'
                    .'<h2>How to launch faster</h2>'
                    .'<p>Prepare your content early, gather your brand assets in one place, consolidate feedback into a single list, and decide who signs off. Do that and even a larger site can move quickly. Ready to start? <a href="/contact">Request your free quote and review call</a>.</p>',
            ],
            [
                'title' => 'Do You Really Need a Website in 2026? Why Social Media Isn\'t Enough',
                'category' => 'Business Growth',
                'tags' => ['small-business', 'conversion', 'affordable'],
                'seo_title' => 'Do You Really Need a Website in 2026?',
                'excerpt' => 'Social media is powerful, but it isn\'t a substitute for your own website. Here\'s why a site you own still matters in 2026 — and when social alone is fine.',
                'body' => '<p>"I already have an Instagram and a Facebook page — do I still need a website?" It\'s one of the most common questions small businesses ask in 2026. Social media is a brilliant way to reach people, but it works best alongside a website you actually own, not instead of one. Here\'s the honest case for both.</p>'
                    .'<h2>You don\'t own your social media</h2>'
                    .'<p>Your followers, posts and reach all live on a platform you don\'t control. Algorithms change, accounts get suspended by mistake, and features you rely on can disappear overnight. A website is the one piece of your online presence that is genuinely yours — nobody can switch it off or bury it in a feed.</p>'
                    .'<h2>A website builds trust that social can\'t</h2>'
                    .'<p>When someone is about to spend money, they often check for a "real" website. A professional site with clear services, pricing guidance, and visible <a href="/privacy">policies</a> signals that you\'re an established, trustworthy business. A social profile alone can leave buyers unsure.</p>'
                    .'<h2>Search is still where buying decisions happen</h2>'
                    .'<p>People actively searching Google for what you offer are often ready to buy. Social media is largely about discovery and browsing; a website lets you capture that high-intent search traffic — which is exactly why <a href="/services">on-page SEO</a> matters so much.</p>'
                    .'<h2>You control the experience and the data</h2>'
                    .'<p>On your own site you decide the layout, the message and the next step — and you can capture enquiries directly rather than hoping a post gets seen. No character limits, no distractions, no competing adverts for other businesses.</p>'
                    .'<h2>When is social media alone okay?</h2>'
                    .'<p>If you\'re testing an idea, run a tiny side hustle, or sell purely through a marketplace, social may be enough to start. But the moment your business becomes something you want to grow and be taken seriously, a website quickly pays for itself.</p>'
                    .'<h2>The good news: it\'s more affordable than you think</h2>'
                    .'<p>A professional website no longer costs thousands. Fixed-price <a href="/pricing">packages</a> start at $199, you own everything, and your site works <em>with</em> your social media — turning followers into enquiries. Not sure what you need? <a href="/contact">Ask us for honest advice</a>.</p>',
            ],
            [
                'title' => 'Website Maintenance: What It Includes and Why It Matters',
                'category' => 'Web Design',
                'tags' => ['maintenance', 'small-business', 'process'],
                'seo_title' => 'Website Maintenance: What It Includes and Why It Matters',
                'excerpt' => 'A website isn\'t "done" at launch. Here\'s what website maintenance actually involves, why it matters, and how to keep your site fast, secure and current.',
                'body' => '<p>Launching a website is a milestone, not a finish line. Like anything valuable, a site needs a little ongoing care to stay fast, secure and effective. Here\'s what website maintenance really involves — and why skipping it tends to cost more in the long run.</p>'
                    .'<h2>What website maintenance actually includes</h2>'
                    .'<h3>Security updates and patches</h3>'
                    .'<p>Software, plugins and components need regular updates to close security holes. Out-of-date sites are a leading target for hackers — most breaches exploit known issues that an update would have fixed.</p>'
                    .'<h3>Backups</h3>'
                    .'<p>Regular, tested backups mean that if something ever goes wrong, your site can be restored quickly instead of rebuilt from scratch.</p>'
                    .'<h3>Performance and uptime checks</h3>'
                    .'<p>Pages can slow down over time as content grows. Periodic checks catch speed and availability issues before your visitors — and Google — notice them.</p>'
                    .'<h3>Content updates</h3>'
                    .'<p>Prices change, offers expire, new services launch. Keeping content current keeps your site accurate and useful, which also helps SEO.</p>'
                    .'<h3>Fixes and small improvements</h3>'
                    .'<p>Broken links, display glitches on a new browser, a form that needs an extra field — small things that are quick to fix when someone\'s looking after the site.</p>'
                    .'<h2>Why it matters</h2>'
                    .'<ul><li><strong>Security:</strong> an unmaintained site is a sitting target.</li>'
                    .'<li><strong>Trust:</strong> errors, outdated info and slow pages quietly cost you customers.</li>'
                    .'<li><strong>SEO:</strong> speed, freshness and working links all influence how you rank.</li>'
                    .'<li><strong>Peace of mind:</strong> you focus on your business, not on your website breaking.</li></ul>'
                    .'<h2>How much maintenance do you need?</h2>'
                    .'<p>It depends on your site. A simple brochure site needs only light, occasional care; a busy store or frequently updated site benefits from more. The key is that <em>someone</em> is responsible for it. Our <a href="/website-maintenance">maintenance and support service</a> keeps things flexible — no long lock-in — and Business and Premium builds include a free support period. <a href="/contact">Ask us what\'s right for your site</a>.</p>',
            ],
            [
                'title' => '5 Signs It\'s Time to Redesign Your Website',
                'category' => 'Web Design',
                'tags' => ['redesign', 'small-business', 'conversion'],
                'seo_title' => '5 Signs It\'s Time to Redesign Your Website',
                'excerpt' => 'Not sure whether your website needs a refresh or a full redesign? Here are five clear signs it\'s time — and how to redesign without losing your SEO.',
                'body' => '<p>Websites age — sometimes gracefully, often not. If yours is starting to feel like a liability rather than an asset, it may be time for a redesign. Here are five clear signs to watch for, and how to modernise without throwing away the SEO you\'ve built.</p>'
                    .'<h2>1. It looks dated</h2>'
                    .'<p>Design trends and expectations move quickly. A site that looked sharp five years ago can now make a business feel behind the times. First impressions form in seconds, and an outdated look quietly erodes trust before a visitor reads a word.</p>'
                    .'<h2>2. It doesn\'t work well on phones</h2>'
                    .'<p>Most traffic is now mobile. If visitors have to pinch, zoom or squint on a phone, you\'re losing them — and Google, which indexes mobile-first, notices too.</p>'
                    .'<h2>3. It\'s slow</h2>'
                    .'<p>Every extra second of load time costs visitors and conversions. If your pages crawl, bloated code and unoptimised images are usually to blame — and a modern rebuild fixes it.</p>'
                    .'<h2>4. It isn\'t bringing in enquiries</h2>'
                    .'<p>If traffic arrives but nobody contacts you, the problem is often unclear messaging and weak calls to action. A redesign focused on conversion turns more of your existing visitors into leads.</p>'
                    .'<h2>5. It\'s painful to update</h2>'
                    .'<p>If making a simple change means wrestling with code or waiting on someone else, you\'ll avoid updating it — and a stale site helps no one. A modern build puts you back in control.</p>'
                    .'<h2>Redesigning without losing your SEO</h2>'
                    .'<p>The biggest fear is losing rankings. Done properly, you won\'t: keep your URL structure where possible, add 301 redirects where pages move, and migrate your content carefully. That\'s exactly how our <a href="/website-redesign">website redesign service</a> works. See the full range of what we do on our <a href="/services">services page</a>, or <a href="/contact">send us your current site</a> for an honest opinion.</p>',
            ],
            [
                'title' => 'Local SEO Basics: How Small Businesses Show Up on Google Maps',
                'category' => 'SEO',
                'tags' => ['local-seo', 'seo', 'small-business'],
                'seo_title' => 'Local SEO Basics: How to Show Up on Google Maps',
                'excerpt' => 'A plain-English guide to local SEO — how small businesses appear in Google Maps and local search, and the practical steps to improve your visibility.',
                'body' => '<p>When someone searches "near me" or looks for a service in their town, Google shows a local map and a handful of businesses above the regular results. Appearing there — in the "local pack" — can bring a steady stream of ready-to-buy customers. Here\'s how local SEO works, in plain English.</p>'
                    .'<h2>What is local SEO?</h2>'
                    .'<p>Local SEO is the practice of helping your business show up when people search for what you offer in a specific area. It\'s driven by a mix of your Google Business Profile, your website, and signals of relevance and trust across the web.</p>'
                    .'<h2>1. Claim and complete your Google Business Profile</h2>'
                    .'<p>This is the single biggest step for most local businesses. Claim your free Google Business Profile, then fill it in fully: accurate category, hours, service area, phone number, photos and a clear description. Keep it up to date.</p>'
                    .'<h2>2. Be consistent with your name, address and phone (NAP)</h2>'
                    .'<p>Make sure your business name, address and phone number are identical everywhere they appear — your website, directories and social profiles. Inconsistencies confuse search engines and dilute your ranking.</p>'
                    .'<h2>3. Make your website locally relevant</h2>'
                    .'<p>Mention the areas you serve naturally in your content, add a contact page with your details, and ensure your site is fast and mobile-friendly. These <a href="/blog/7-on-page-seo-basics-every-new-website-needs">on-page SEO basics</a> underpin local ranking too.</p>'
                    .'<h2>4. Earn genuine reviews</h2>'
                    .'<p>Honest reviews from real customers are a strong local signal — and they build trust with buyers. Ask happy customers to leave a review, and respond politely to the ones you receive. Never buy fake reviews; it breaches Google\'s policies and backfires.</p>'
                    .'<h2>5. Get listed in relevant directories</h2>'
                    .'<p>Being listed accurately in reputable local and industry directories reinforces your legitimacy and can send referral traffic.</p>'
                    .'<h2>A note on expectations</h2>'
                    .'<p>Local SEO rewards consistency over time — there are no overnight shortcuts, and no one can guarantee the top spot. But the fundamentals above are within reach of any small business. Want the technical groundwork handled on your site? See our <a href="/seo-services">SEO services</a> or <a href="/contact">get in touch</a>.</p>',
            ],
            [
                'title' => 'How to Choose the Perfect Domain Name for Your Business',
                'category' => 'Business Growth',
                'tags' => ['domain', 'small-business', 'branding'],
                'seo_title' => 'How to Choose the Perfect Domain Name for Your Business',
                'excerpt' => 'Your domain name is your address on the web and a big part of your brand. Here\'s how to choose one that\'s memorable, trustworthy and future-proof.',
                'body' => '<p>Your domain name — the <code>yourbusiness.com</code> part of your web address — is often the first thing people see and the thing they\'ll type, share and remember. Choosing well pays off for years. Here\'s how to get it right.</p>'
                    .'<h2>Keep it short, simple and memorable</h2>'
                    .'<p>The best domains are easy to say, spell and remember. Avoid hyphens, numbers and unusual spellings that are hard to dictate over the phone. If you have to explain how to spell it, it\'s probably too complicated.</p>'
                    .'<h2>Make it brandable</h2>'
                    .'<p>A name that reflects your brand usually beats stuffing in keywords. "Keyword" domains once helped SEO; today a strong, trustworthy brand name serves you better and is easier to grow with.</p>'
                    .'<h2>Pick the right extension</h2>'
                    .'<p><strong>.com</strong> is still the most trusted and expected extension worldwide. If your .com is taken, a relevant country extension (like .co.uk) or a modern one (.co, .io) can work — but people will often still try .com first.</p>'
                    .'<h2>Check availability everywhere</h2>'
                    .'<p>Before committing, check the domain <em>and</em> matching social handles are free, and that the name isn\'t trademarked by someone else. Consistency across your website and social profiles builds trust.</p>'
                    .'<h2>Think long term</h2>'
                    .'<p>Avoid tying the name too tightly to one product or city if you plan to expand. "JanesCardiffCakes" is limiting; "JanesBakes" leaves room to grow.</p>'
                    .'<h2>Register it in your own name</h2>'
                    .'<p>Always own your domain yourself — never let a provider register it on your behalf. It\'s part of owning your online presence, which is exactly why we build sites you fully own. Ready to launch? <a href="/pricing">See our packages</a> or <a href="/contact">get a free quote</a>, and we\'ll help you set up the right domain.</p>',
            ],
            [
                'title' => 'What Is Web Hosting? A Plain-English Guide for Beginners',
                'category' => 'Business Growth',
                'tags' => ['hosting', 'small-business', 'domain'],
                'seo_title' => 'What Is Web Hosting? A Plain-English Guide for Beginners',
                'excerpt' => 'Hosting, domains, DNS — the jargon can be confusing. This beginner-friendly guide explains what web hosting is and how to choose a reliable host.',
                'body' => '<p>If you\'re getting your first website, you\'ll quickly run into words like "hosting", "domain" and "DNS". They sound technical, but the ideas are simple. Here\'s the plain-English version.</p>'
                    .'<h2>What is web hosting?</h2>'
                    .'<p>Your website is a set of files. Those files need to live on a computer that\'s connected to the internet 24/7 so anyone can visit. That computer is a <strong>server</strong>, and renting space on it is <strong>web hosting</strong>. Think of hosting as the land your house (website) is built on.</p>'
                    .'<h2>How hosting and domains work together</h2>'
                    .'<p>Your <strong>domain</strong> (yourbusiness.com) is your address. Your <strong>hosting</strong> is where the website actually lives. <strong>DNS</strong> is the system that connects the two — pointing your domain to your host so visitors arrive at the right place.</p>'
                    .'<h2>Types of hosting</h2>'
                    .'<ul><li><strong>Shared hosting:</strong> affordable; your site shares a server with others. Great for most small business sites.</li>'
                    .'<li><strong>VPS / cloud:</strong> more power and control for busier sites.</li>'
                    .'<li><strong>Managed hosting:</strong> the host handles updates and security for you, at a higher price.</li></ul>'
                    .'<h2>What to look for in a host</h2>'
                    .'<p>Reliable uptime, fast servers (ideally near your customers), free SSL (the padlock/HTTPS), easy backups, and responsive support. Speed matters for both visitors and <a href="/blog/7-on-page-seo-basics-every-new-website-needs">SEO</a>.</p>'
                    .'<h2>Keep hosting in your own name</h2>'
                    .'<p>We don\'t resell hosting — we help you choose a reliable host and keep everything in your own name, so you\'re never locked in. Questions about getting set up? <a href="/contact">Just ask</a> — we guide every client through it at no extra charge.</p>',
            ],
            [
                'title' => '9 Trust Signals That Turn Website Visitors Into Customers',
                'category' => 'Web Design',
                'tags' => ['conversion', 'small-business', 'branding'],
                'seo_title' => '9 Trust Signals That Turn Website Visitors Into Customers',
                'excerpt' => 'People buy from businesses they trust. Here are nine practical trust signals that make your website more credible and convert more visitors.',
                'body' => '<p>Online, people can\'t shake your hand or walk into your shop — so your website has to earn trust on its own. These nine trust signals quietly reassure visitors and turn more of them into enquiries and customers.</p>'
                    .'<h2>1. A professional, modern design</h2>'
                    .'<p>Like it or not, people judge credibility on looks in seconds. A clean, current design signals a real, capable business.</p>'
                    .'<h2>2. Clear contact details</h2>'
                    .'<p>A visible email, phone or contact form tells visitors there are real people behind the site. Hidden contact info raises doubt.</p>'
                    .'<h2>3. An "About" page with a human touch</h2>'
                    .'<p>Who you are, what you believe, and who you help. People connect with people — see how we do it on our <a href="/about">About page</a>.</p>'
                    .'<h2>4. Real testimonials and reviews</h2>'
                    .'<p>Genuine words from real customers are powerful. (Never fake them — it backfires and breaks trust fast.)</p>'
                    .'<h2>5. Fast, secure pages (HTTPS)</h2>'
                    .'<p>The padlock in the address bar and quick load times reassure visitors their data is safe and the business is legitimate.</p>'
                    .'<h2>6. Transparent pricing</h2>'
                    .'<p>Clear, upfront pricing removes anxiety. It\'s exactly why our <a href="/pricing">prices are public and fixed</a>.</p>'
                    .'<h2>7. Visible policies</h2>'
                    .'<p>A <a href="/privacy">privacy policy</a> and terms show you take the relationship seriously.</p>'
                    .'<h2>8. Consistent branding</h2>'
                    .'<p>The same name, logo and tone across your site and social profiles signals a coherent, established business.</p>'
                    .'<h2>9. Helpful content</h2>'
                    .'<p>A useful blog or FAQ shows expertise and generosity — and builds confidence before anyone even contacts you.</p>'
                    .'<p>Want a website built to earn trust from the first second? <a href="/contact">Let\'s talk</a>.</p>',
            ],
            [
                'title' => 'Why Website Speed Matters (and How to Make Your Site Faster)',
                'category' => 'SEO',
                'tags' => ['speed', 'conversion', 'seo'],
                'seo_title' => 'Why Website Speed Matters and How to Make Your Site Faster',
                'excerpt' => 'A slow website costs you visitors, sales and rankings. Here\'s why speed matters so much — and practical ways to make your site faster.',
                'body' => '<p>Website speed isn\'t a nice-to-have — it directly affects how many visitors stay, buy and come back. Study after study shows that even a one-second delay can measurably cut conversions. Here\'s why it matters and how to fix it.</p>'
                    .'<h2>Why speed matters</h2>'
                    .'<ul><li><strong>Conversions:</strong> faster pages keep more visitors from leaving before they even see your offer.</li>'
                    .'<li><strong>SEO:</strong> Google uses page speed and Core Web Vitals as ranking signals.</li>'
                    .'<li><strong>Experience:</strong> a snappy site simply feels more professional and trustworthy.</li></ul>'
                    .'<h2>What slows sites down</h2>'
                    .'<p>Usually it\'s large, unoptimised images, bloated themes and plugins, too much code, and slow or distant hosting.</p>'
                    .'<h2>How to make your site faster</h2>'
                    .'<h3>1. Optimise your images</h3>'
                    .'<p>Images are the biggest culprit. Compress them and use modern formats like WebP. Never upload a 4000px photo to display at 400px.</p>'
                    .'<h3>2. Keep the code lean</h3>'
                    .'<p>Avoid heavy page builders and endless plugins. Clean, purpose-built code (what we hand-build) loads far faster.</p>'
                    .'<h3>3. Use good hosting and caching</h3>'
                    .'<p>A quality host and caching serve pages quickly, especially to repeat visitors.</p>'
                    .'<h3>4. Minimise third-party scripts</h3>'
                    .'<p>Every tracker, chat widget and font adds weight. Keep only what earns its place.</p>'
                    .'<h2>Measure it</h2>'
                    .'<p>Test your site with a free tool like Google PageSpeed Insights to see your Core Web Vitals and specific fixes. Want a fast site without the hassle? Speed is built into every site we make — <a href="/website-design">see our web design service</a> or <a href="/contact">request a quote</a>.</p>',
            ],
            [
                'title' => 'How to Write Website Content That Actually Sells',
                'category' => 'Marketing',
                'tags' => ['content', 'conversion', 'small-business'],
                'seo_title' => 'How to Write Website Content That Actually Sells',
                'excerpt' => 'Great web design gets attention; great words win the sale. Here\'s how to write clear, persuasive website copy that converts — without sounding pushy.',
                'body' => '<p>You can have a beautiful website that still doesn\'t convert — because the words aren\'t doing their job. Good copy is clear, customer-focused and persuasive without being pushy. Here\'s how to write it.</p>'
                    .'<h2>Lead with the customer, not yourself</h2>'
                    .'<p>Visitors care about <em>their</em> problem. Open with what they want to achieve, then show how you help. Swap "We are a leading agency" for "Get a professional website that brings in customers".</p>'
                    .'<h2>Focus on benefits, not just features</h2>'
                    .'<p>A feature is what something is; a benefit is what it does for them. "Mobile-friendly design" is a feature; "looks perfect on every phone, so you never lose a customer" is the benefit.</p>'
                    .'<h2>Write the way people talk</h2>'
                    .'<p>Use short sentences, plain words and a warm, human tone. Read it aloud — if it sounds stiff, simplify it. Avoid jargon your customers wouldn\'t use.</p>'
                    .'<h2>Make it scannable</h2>'
                    .'<p>People skim. Use clear headings, short paragraphs and bullet points so visitors can grasp the gist in seconds and dive deeper where they want.</p>'
                    .'<h2>Use clear calls to action</h2>'
                    .'<p>Tell people exactly what to do next: "Get a free quote", "Book a call", "View pricing". One obvious next step per page beats five competing links.</p>'
                    .'<h2>Back up your claims</h2>'
                    .'<p>Specifics and proof beat empty superlatives. "Websites from $199, delivered in 1–3 weeks" is more persuasive than "amazing value, super fast".</p>'
                    .'<p>Not a writer? That\'s fine — we help shape your content as part of every build. <a href="/contact">Tell us about your business</a> and we\'ll help you say it well.</p>',
            ],
            [
                'title' => 'Does Your Business Really Need a Blog? The Honest Answer',
                'category' => 'Marketing',
                'tags' => ['content', 'seo', 'small-business'],
                'seo_title' => 'Does Your Business Really Need a Blog? The Honest Answer',
                'excerpt' => 'Blogging is often recommended — but is it worth it for your business? An honest look at when a blog pays off and when to skip it.',
                'body' => '<p>"You should start a blog" is common advice — but it\'s not right for everyone. Here\'s an honest look at what a business blog can do, when it\'s worth it, and when your effort is better spent elsewhere.</p>'
                    .'<h2>What a blog actually does</h2>'
                    .'<ul><li><strong>Brings in search traffic:</strong> each helpful article is a new chance to rank for what customers search.</li>'
                    .'<li><strong>Builds trust and authority:</strong> useful content shows you know your field.</li>'
                    .'<li><strong>Feeds your other channels:</strong> posts give you something to share on social and in emails.</li></ul>'
                    .'<h2>When a blog is worth it</h2>'
                    .'<p>If your customers research before they buy, if you can answer their common questions, and if you can publish consistently (even once or twice a month), a blog can compound into a real source of free traffic over time — just like the guides you\'re reading here.</p>'
                    .'<h2>When to skip it (for now)</h2>'
                    .'<p>If you can\'t commit to publishing regularly, a stale, abandoned blog does little. In that case, focus first on strong core pages and <a href="/seo-services">on-page SEO</a>, and add a blog later when you have capacity.</p>'
                    .'<h2>Quality beats quantity</h2>'
                    .'<p>One genuinely helpful, well-targeted article beats ten thin, keyword-stuffed ones. Write for people first; search engines reward content that actually helps.</p>'
                    .'<h2>Make it easy to manage</h2>'
                    .'<p>A blog only works if publishing is simple. Our <a href="/pricing">Premium package</a> includes a blog you can update yourself in minutes. Not sure if it\'s right for you? <a href="/contact">Ask us</a> — we\'ll give you an honest answer.</p>',
            ],
            [
                'title' => 'How to Start Selling Online: An E-Commerce Guide for 2026',
                'category' => 'E-Commerce',
                'tags' => ['ecommerce', 'small-business', 'conversion'],
                'seo_title' => 'How to Start Selling Online: An E-Commerce Guide for 2026',
                'excerpt' => 'Thinking of selling online? This beginner\'s guide walks through the essentials of starting an e-commerce store in 2026 — from products to payments.',
                'body' => '<p>Selling online opens your business to customers far beyond your local area. But a store is more involved than a brochure site. Here\'s a clear, beginner-friendly roadmap to launching e-commerce in 2026.</p>'
                    .'<h2>1. Get clear on what you\'re selling</h2>'
                    .'<p>How many products? Physical or digital? Any variations (size, colour)? The answers shape everything from your platform to your shipping setup.</p>'
                    .'<h2>2. Nail your product pages</h2>'
                    .'<p>Clear photos, honest descriptions, visible prices and obvious "Add to cart" buttons. Product pages are where buying decisions happen — don\'t make people guess.</p>'
                    .'<h2>3. Make checkout effortless</h2>'
                    .'<p>Every extra step loses sales. Keep checkout short, offer the payment methods your customers use, and never surprise them with fees at the last moment.</p>'
                    .'<h2>4. Set up payments and shipping</h2>'
                    .'<p>Choose a secure payment gateway, and decide your shipping rates and regions up front. Clear delivery info builds trust and cuts abandoned carts.</p>'
                    .'<h2>5. Build in trust</h2>'
                    .'<p>Show secure-checkout badges, clear return and <a href="/privacy">privacy</a> policies, and real contact details. Online shoppers need reassurance before they pay.</p>'
                    .'<h2>6. Plan for mobile</h2>'
                    .'<p>Most shopping now happens on phones. Your store must be fast and effortless to use on a small screen.</p>'
                    .'<h2>7. Get found</h2>'
                    .'<p>Optimise product pages for search and consider content that attracts buyers, as covered in our <a href="/blog/7-on-page-seo-basics-every-new-website-needs">on-page SEO guide</a>.</p>'
                    .'<p>Ready to sell online? We build managed, mobile-first stores you control. <a href="/ecommerce-website-development">See our e-commerce service</a> or <a href="/contact">request a quote</a>.</p>',
            ],
            [
                'title' => 'How Many Pages Does a Small Business Website Need?',
                'category' => 'Web Design',
                'tags' => ['small-business', 'process', 'affordable'],
                'seo_title' => 'How Many Pages Does a Small Business Website Need?',
                'excerpt' => 'Not sure how many pages your website should have? Here\'s a practical guide to the pages most small businesses need — and which ones to skip.',
                'body' => '<p>One of the most common questions we hear is "how many pages do I actually need?" The honest answer: fewer than you might think, done well. Here\'s a practical guide.</p>'
                    .'<h2>The core pages almost every business needs</h2>'
                    .'<ul><li><strong>Home</strong> — your shop window: who you are, what you offer, and the next step.</li>'
                    .'<li><strong>About</strong> — the people and story behind the business, to build trust.</li>'
                    .'<li><strong>Services / Products</strong> — what you offer and who it\'s for.</li>'
                    .'<li><strong>Contact</strong> — easy ways to reach you, ideally with a form.</li></ul>'
                    .'<p>That four-to-five page "starter" site covers most small businesses — which is exactly why our <a href="/pricing">Starter package</a> is built around it.</p>'
                    .'<h2>Pages that add value as you grow</h2>'
                    .'<ul><li><strong>Individual service pages</strong> — one per key service, great for SEO (see our own <a href="/services">services</a>).</li>'
                    .'<li><strong>Pricing</strong> — if you can be transparent, it builds trust and saves time.</li>'
                    .'<li><strong>Portfolio / case studies</strong> — proof of your work.</li>'
                    .'<li><strong>FAQ</strong> — answers common questions and helps SEO.</li>'
                    .'<li><strong>Blog</strong> — for ongoing content and search traffic.</li></ul>'
                    .'<h2>Pages to be careful with</h2>'
                    .'<p>Avoid thin pages that exist only to pad the count — a handful of empty pages hurts more than it helps. Every page should earn its place with a clear purpose.</p>'
                    .'<h2>Quality over quantity</h2>'
                    .'<p>A focused five-page site that\'s clear and fast beats a sprawling twenty-page one nobody finishes reading. Not sure what you need? <a href="/contact">Tell us your goals</a> and we\'ll recommend honestly.</p>',
            ],
            [
                'title' => 'Mobile-First Design: Why Your Website Must Work on Phones',
                'category' => 'Web Design',
                'tags' => ['mobile', 'conversion', 'small-business'],
                'seo_title' => 'Mobile-First Design: Why Your Website Must Work on Phones',
                'excerpt' => 'More than half of web traffic is on phones. If your site isn\'t built mobile-first, you\'re losing customers. Here\'s what mobile-first really means.',
                'body' => '<p>Open your website on your phone right now. Is the text readable without zooming? Are buttons easy to tap? If not, you\'re quietly losing customers — because most of your visitors are on mobile.</p>'
                    .'<h2>Why mobile matters so much</h2>'
                    .'<ul><li><strong>Most traffic is mobile:</strong> in many industries, the majority of visitors arrive on a phone.</li>'
                    .'<li><strong>Google indexes mobile-first:</strong> it primarily looks at your mobile site to decide rankings.</li>'
                    .'<li><strong>Conversions depend on it:</strong> a frustrating mobile experience sends buyers straight to a competitor.</li></ul>'
                    .'<h2>What "mobile-first" actually means</h2>'
                    .'<p>It means designing for the smallest screen first, then scaling up — rather than cramming a desktop design onto a phone. The result is a site that feels natural on mobile, not an afterthought.</p>'
                    .'<h2>The hallmarks of a good mobile site</h2>'
                    .'<ul><li>Readable text without pinching or zooming.</li>'
                    .'<li>Big, easy-to-tap buttons and links.</li>'
                    .'<li>Fast loading on mobile data.</li>'
                    .'<li>Simple navigation that works with a thumb.</li>'
                    .'<li>Forms that are quick to complete on a small screen.</li></ul>'
                    .'<h2>How to check your site</h2>'
                    .'<p>Browse your own site on a phone and try to complete a key task — like finding your phone number or sending an enquiry. If it\'s fiddly, your customers feel it too.</p>'
                    .'<p>Every website we build is mobile-first and tested across devices as standard. If yours isn\'t working on phones, a <a href="/website-redesign">redesign</a> can fix it — <a href="/contact">get in touch</a>.</p>',
            ],
            [
                'title' => 'Website Security Basics Every Small Business Should Know',
                'category' => 'Web Design',
                'tags' => ['security', 'small-business', 'maintenance'],
                'seo_title' => 'Website Security Basics Every Small Business Should Know',
                'excerpt' => 'You don\'t need to be a security expert to protect your website. Here are the essential, practical security basics every small business should have in place.',
                'body' => '<p>Small business websites get attacked far more often than owners realise — usually by automated bots looking for easy targets, not personal attention. The good news: a few basics keep you off the easy-target list.</p>'
                    .'<h2>1. Use HTTPS (SSL)</h2>'
                    .'<p>An SSL certificate encrypts data between your site and visitors and shows the padlock in the address bar. It\'s expected by visitors, required for trust, and a small ranking factor. Most good hosts offer it free.</p>'
                    .'<h2>2. Keep everything updated</h2>'
                    .'<p>Out-of-date software, themes and plugins are the number one way sites get hacked. Regular updates close known holes — a core part of <a href="/website-maintenance">ongoing maintenance</a>.</p>'
                    .'<h2>3. Use strong, unique passwords</h2>'
                    .'<p>Use a password manager, enable two-factor authentication on your admin and hosting accounts, and never reuse passwords.</p>'
                    .'<h2>4. Back up regularly</h2>'
                    .'<p>If the worst happens, a recent backup means you can restore quickly instead of rebuilding from scratch. Automate it where you can.</p>'
                    .'<h2>5. Limit and review access</h2>'
                    .'<p>Give people only the access they need, and remove accounts that are no longer used. Fewer doors mean fewer ways in.</p>'
                    .'<h2>6. Choose quality hosting and lean code</h2>'
                    .'<p>A reputable host and a site without dozens of risky plugins is inherently safer. Clean, purpose-built code has a smaller attack surface.</p>'
                    .'<h2>Security is ongoing, not one-off</h2>'
                    .'<p>Protection isn\'t "set and forget" — it needs a little regular attention. We build sites securely and offer <a href="/website-maintenance">care plans</a> to keep them that way. <a href="/contact">Ask us</a> what\'s right for your site.</p>',
            ],
            [
                'title' => 'How to Prepare Your Content Before a Website Build',
                'category' => 'Web Design',
                'tags' => ['process', 'content', 'small-business'],
                'seo_title' => 'How to Prepare Your Content Before a Website Build',
                'excerpt' => 'The biggest cause of delayed website projects is missing content. Here\'s how to prepare your text, images and brand assets so your build goes smoothly.',
                'body' => '<p>Here\'s an open secret from the web world: the thing that slows most projects down isn\'t design or development — it\'s waiting on content. Prepare it well and your build will be faster, smoother and better. Here\'s how.</p>'
                    .'<h2>Gather your brand assets</h2>'
                    .'<p>Collect your logo (in the highest quality you have), your brand colours, and any fonts or style guides. Don\'t have a logo yet? Note that early so it can be sorted.</p>'
                    .'<h2>Write (or outline) your page text</h2>'
                    .'<p>For each page, jot down the key message and the action you want visitors to take. Even rough notes help — a good studio will refine the wording with you. See the <a href="/blog/how-to-write-website-content-that-actually-sells">copywriting tips</a> for guidance.</p>'
                    .'<h2>Collect your images</h2>'
                    .'<p>Real photos of your work, team or premises build far more trust than generic stock. Gather the best you have at good resolution. Quality beats quantity.</p>'
                    .'<h2>List your must-haves</h2>'
                    .'<p>Note the essentials: contact details, opening hours, services, social links, and any features like a booking form or gallery.</p>'
                    .'<h2>Find examples you like</h2>'
                    .'<p>A few websites you admire (and what you like about them) give your designer a head start and avoid guesswork.</p>'
                    .'<h2>Decide who signs off</h2>'
                    .'<p>Agree who gives feedback and final approval. Scattered, conflicting feedback is a major cause of delays.</p>'
                    .'<p>Prepared content means a quicker launch — often within our <a href="/how-it-works">standard timeline</a>. Don\'t have everything ready? We guide every client through it. <a href="/contact">Start your project</a> and we\'ll help you organise it.</p>',
            ],
            [
                'title' => 'Google Business Profile: A Step-by-Step Setup Guide',
                'category' => 'SEO',
                'tags' => ['local-seo', 'seo', 'small-business'],
                'seo_title' => 'Google Business Profile: A Step-by-Step Setup Guide',
                'excerpt' => 'A free Google Business Profile is one of the most powerful tools for local visibility. Here\'s a step-by-step guide to setting yours up properly.',
                'body' => '<p>If you serve customers in a specific area, a Google Business Profile is one of the highest-impact (and free) things you can do for visibility. It\'s what puts you on Google Maps and in the local results. Here\'s how to set it up.</p>'
                    .'<h2>Step 1: Claim your profile</h2>'
                    .'<p>Go to Google Business Profile and search for your business. Claim it if it exists, or create it if it doesn\'t. You\'ll verify ownership (usually by phone, email or postcard).</p>'
                    .'<h2>Step 2: Complete every field</h2>'
                    .'<p>Fill in everything: exact business name, category, address or service area, phone, website, and hours. A complete profile ranks and converts better than a sparse one.</p>'
                    .'<h2>Step 3: Add photos</h2>'
                    .'<p>Upload real, high-quality photos of your premises, team, products or work. Profiles with good photos get noticeably more clicks and calls.</p>'
                    .'<h2>Step 4: Keep your details consistent</h2>'
                    .'<p>Your name, address and phone number should match exactly across your website and other listings. Inconsistencies confuse Google and dilute your ranking — more on that in our <a href="/blog/local-seo-basics-how-small-businesses-show-up-on-google-maps">local SEO guide</a>.</p>'
                    .'<h2>Step 5: Collect genuine reviews</h2>'
                    .'<p>Politely ask happy customers to leave a review, and respond to the ones you get. Never buy fake reviews — it breaches Google\'s policies and erodes trust.</p>'
                    .'<h2>Step 6: Keep it fresh</h2>'
                    .'<p>Post updates, offers and new photos now and then. An active profile signals a live, trustworthy business.</p>'
                    .'<p>Want your website and local presence working together? <a href="/seo-services">See our SEO services</a> or <a href="/contact">get in touch</a>.</p>',
            ],
            [
                'title' => '8 Ways to Get More Leads From Your Website',
                'category' => 'Business Growth',
                'tags' => ['leads', 'conversion', 'small-business'],
                'seo_title' => '8 Ways to Get More Leads From Your Website',
                'excerpt' => 'Your website should be your hardest-working salesperson. Here are eight practical ways to turn more of your existing visitors into leads and enquiries.',
                'body' => '<p>Getting traffic is only half the job — the other half is turning visitors into leads. If people arrive but few get in touch, these eight practical changes can help, often without any extra traffic at all.</p>'
                    .'<h2>1. Make your call to action obvious</h2>'
                    .'<p>Every page should make the next step clear and easy: "Get a free quote", "Book a call". Repeat it so visitors never have to hunt for it.</p>'
                    .'<h2>2. Keep forms short</h2>'
                    .'<p>Ask only for what you truly need. Every extra field costs you submissions. You can always gather details later.</p>'
                    .'<h2>3. Add trust signals near the action</h2>'
                    .'<p>Place reassurance — a guarantee, a testimonial, "no obligation" — right next to your buttons and forms to reduce hesitation.</p>'
                    .'<h2>4. Speed up your site</h2>'
                    .'<p>Slow pages lose leads before they load. Faster sites convert better, as covered in our <a href="/blog/why-website-speed-matters-and-how-to-make-your-site-faster">speed guide</a>.</p>'
                    .'<h2>5. Be reachable your way</h2>'
                    .'<p>Offer the contact methods your customers prefer — form, email, phone or chat. The easier it is to reach you, the more will.</p>'
                    .'<h2>6. Use clear, benefit-led headlines</h2>'
                    .'<p>Tell visitors what\'s in it for them within seconds of landing. Confusion kills conversions.</p>'
                    .'<h2>7. Show proof</h2>'
                    .'<p>Real examples, results and reviews reassure buyers they\'re making a safe choice.</p>'
                    .'<h2>8. Follow up quickly</h2>'
                    .'<p>Leads go cold fast. Responding promptly dramatically improves your chances of winning the work.</p>'
                    .'<p>Want a website built to generate enquiries, not just look nice? That\'s what we do — <a href="/contact">let\'s talk</a>.</p>',
            ],
            [
                'title' => 'Website Accessibility: What It Is and Why It Matters',
                'category' => 'Web Design',
                'tags' => ['accessibility', 'small-business', 'conversion'],
                'seo_title' => 'Website Accessibility: What It Is and Why It Matters',
                'excerpt' => 'Accessible websites are easier for everyone to use — and they widen your audience, reduce legal risk and often rank better. Here\'s a practical introduction.',
                'body' => '<p>Web accessibility means building sites that everyone can use, including people with visual, hearing, motor or cognitive differences. It\'s the right thing to do — and it\'s also good for business. Here\'s a plain introduction.</p>'
                    .'<h2>Why accessibility matters</h2>'
                    .'<ul><li><strong>Wider audience:</strong> a significant share of people have some form of disability — excluding them means losing customers.</li>'
                    .'<li><strong>Better for everyone:</strong> clear text, good contrast and simple navigation help all users, especially on phones.</li>'
                    .'<li><strong>SEO benefits:</strong> many accessibility practices (alt text, headings, clear structure) also help search engines.</li>'
                    .'<li><strong>Reduced risk:</strong> in some regions, accessibility is a legal requirement.</li></ul>'
                    .'<h2>Accessibility basics</h2>'
                    .'<h3>Good colour contrast</h3>'
                    .'<p>Text should stand out clearly from its background so it\'s readable for everyone, including in bright sunlight.</p>'
                    .'<h3>Descriptive alt text</h3>'
                    .'<p>Describe meaningful images so screen readers (and search engines) understand them.</p>'
                    .'<h3>Clear structure and headings</h3>'
                    .'<p>Logical headings help people navigate with assistive technology and help everyone scan.</p>'
                    .'<h3>Keyboard-friendly navigation</h3>'
                    .'<p>Everything should work without a mouse — many people navigate by keyboard.</p>'
                    .'<h3>Readable text and labelled forms</h3>'
                    .'<p>Use a sensible text size and clearly label every form field.</p>'
                    .'<h2>You don\'t have to be perfect</h2>'
                    .'<p>Accessibility is a spectrum — getting the basics right already helps a lot. We build these fundamentals into every site as standard. <a href="/contact">Ask us</a> about making your site more inclusive.</p>',
            ],
            [
                'title' => 'Core Web Vitals Explained Without the Jargon',
                'category' => 'SEO',
                'tags' => ['speed', 'seo', 'conversion'],
                'seo_title' => 'Core Web Vitals Explained Without the Jargon',
                'excerpt' => 'Core Web Vitals sound technical, but the idea is simple: is your page fast, stable and responsive? Here\'s what they mean in plain English.',
                'body' => '<p>"Core Web Vitals" is a phrase you\'ll see whenever website speed comes up. It sounds intimidating, but it\'s really just Google\'s way of measuring whether a page feels fast and pleasant to use. Here it is without the jargon.</p>'
                    .'<h2>What Core Web Vitals measure</h2>'
                    .'<p>There are three main ones, each measuring part of the experience:</p>'
                    .'<h3>1. Loading — does it appear quickly? (LCP)</h3>'
                    .'<p>Largest Contentful Paint measures how long the main content takes to show up. Aim for the biggest thing on screen to load within about 2.5 seconds.</p>'
                    .'<h3>2. Responsiveness — does it react quickly? (INP)</h3>'
                    .'<p>Interaction to Next Paint measures how fast the page responds when you tap or click. A snappy page feels responsive; a laggy one feels broken.</p>'
                    .'<h3>3. Stability — does it stay put? (CLS)</h3>'
                    .'<p>Cumulative Layout Shift measures unexpected movement — like a button jumping just as you tap it because an image loaded late. Less shifting is better.</p>'
                    .'<h2>Why they matter</h2>'
                    .'<p>Google uses Core Web Vitals as a ranking signal, and they closely track how real visitors feel. A page that loads fast, responds instantly and doesn\'t jump around keeps more visitors and converts better — see our <a href="/blog/why-website-speed-matters-and-how-to-make-your-site-faster">guide to website speed</a>.</p>'
                    .'<h2>How to check yours</h2>'
                    .'<p>Run your site through Google PageSpeed Insights for a free report and specific fixes. Want them handled for you? Good Core Web Vitals are built into every site we make — <a href="/website-design">see our web design service</a> or <a href="/contact">request a quote</a>.</p>',
            ],
        ];
    }
}
