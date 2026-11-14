# Page-wise Content & SEO Mapping

**Project:** Webveo CMS · **Date:** 2026-11-09

All titles/descriptions below are stored **brand-free**; the site name (`Webveo`, editable in Settings) is appended automatically by `SeoService`, e.g. `About Us — Affordable Website Design Agency | Webveo`.
Everything here is editable in **Admin → Pages / Packages / Blog → SEO fields**.

---

## Core pages

| Page / URL | Primary keyword | Search intent | SEO title (stored) | Meta description | H1 |
|---|---|---|---|---|---|
| **Home** `/` | affordable website design | Commercial | Professional Websites at Affordable Prices | Get a professionally designed, mobile-ready website from $199. Fixed one-time pricing, fast delivery, SEO-ready, full ownership. Serving the USA, UK, Canada, Australia & New Zealand. | Professional Websites at **Affordable Prices** |
| **About** `/about` | affordable web design agency | Informational | About Us — Affordable Website Design Agency | A remote-first web design studio helping small businesses in the USA, UK, Canada, Australia & New Zealand launch professional websites at honest, fixed prices. | About Us |
| **Services** `/services` | web design services | Commercial | Web Design, Development & SEO Services | Professional website design, development, e-commerce, redesigns, on-page SEO and ongoing support — built for small businesses at affordable fixed prices. | Services |
| **Industries** `/industries` | website design for small business industries | Commercial | Industries We Serve — Website Design for Every Business | Affordable websites tailored to your industry: restaurants, clinics, trades, consultants, startups, online stores, real estate and nonprofits — for businesses worldwide. | Industries We Serve |
| **Our Work** `/portfolio` | web design portfolio | Commercial | Our Work — Website Design Portfolio & Examples | See the kind of websites we design and build and what goes into every project — business sites, stores, redesigns, booking and landing pages. | Our Work |
| **How It Works** `/how-it-works` | website design process | Informational | How It Works — Our Website Design Process | See our simple four-step website process: submit requirements, a free 15-minute review call, design and build, then launch and support. | How It Works |
| **FAQ** `/faq` | website design FAQ | Informational | Frequently Asked Questions About Our Websites | Clear answers on website pricing, delivery times, ownership, hosting, SEO, revisions, payments and support. | FAQ |
| **Pricing** `/pricing` | website design packages pricing | Commercial/Transactional | Website Pricing & Packages from $199 | Affordable, transparent website packages starting at $199 one-time. Compare Starter, Business, Premium and Custom plans — no monthly lock-in, full ownership. | Pricing |
| **Contact** `/contact` | get a website quote | Transactional | Contact Us — Get Your Website Quote | Share your project requirements and we'll reply quickly with a free quote and a 15-minute review meeting. | Contact Us |
| **Privacy** `/privacy` | — | Trust/Legal | Privacy Policy | How we collect, use, store and protect the personal information you share with us. | Privacy Policy |
| **Terms** `/terms` | — | Trust/Legal | Terms of Service | The terms and conditions that govern your use of our website and services. | Terms of Service |
| **Careers** `/careers` | web design agency careers | Informational/Transactional | Careers — Join Our Remote Web Design Team | We're a remote-first studio of 50+ developers and designers, hiring worldwide. Explore open roles and apply. | Build the web with a team of 50+ developers |
| **Job detail** `/careers/{slug}` | [role] job | Transactional | [Role] — Careers | Per-role summary. | [Role title] |
| **Blog index** `/blog` | web design blog | Informational | Blog — Web Design & Online Growth Tips | Practical, honest articles on web design, website pricing, on-page SEO and converting visitors. | Blog & Insights |

---

## Service detail pages (topic cluster)

Dynamic CMS pages (admin-editable) that expand the `/services` hub into a hub-and-spoke cluster. Each has a richtext intro (internal links), a "what's included" grid, visible FAQs (→ FAQPage schema) and, where relevant, the live pricing section. Linked site-wide from the footer "Our Services" column and from the Services hub feature cards.

| Page / URL | Primary keyword | SEO title (stored) | H1 |
|---|---|---|---|
| **Website Design** `/website-design` | website design services | Website Design Services — Affordable & Mobile-First | Website Design Services |
| **E-Commerce Development** `/ecommerce-website-development` | ecommerce website development | E-Commerce Website Development — Start Selling Online | E-Commerce Website Development |
| **Website Redesign** `/website-redesign` | website redesign services | Website Redesign Services — Modernise Without Losing SEO | Website Redesign Services |
| **Website Maintenance** `/website-maintenance` | website maintenance services | Website Maintenance & Support Services | Website Maintenance & Support |
| **SEO Services** `/seo-services` | on-page SEO services | On-Page & Technical SEO Services for Small Business | On-Page & Technical SEO Services |

> The SEO page states plainly that no agency can guarantee a #1 ranking — honest, people-first content, no over-promising.

---

## Package pages (`/packages/{slug}`)

Schema: `Service` + `Offer` (price, USD, InStock) + `BreadcrumbList`.

| Package | Price | Focus | SEO title (stored) |
|---|---|---|---|
| Starter Website | $199 | 5-page starter site | Starter Website — $199 Website Package |
| Business Website | $299 | 10-page + forms + support | Business Website — $299 Website Package |
| Premium Website | $599 | 20-page + blog + support | Premium Website — $599 Website Package |
| Custom Website | Custom | E-commerce / bespoke | Custom Website — Custom Website Package |

---

## Blog posts (`/blog/{slug}`)

Schema: `BlogPosting` + `BreadcrumbList`; `og:type=article`.

| Title | Category | Target keyword | Internal links |
|---|---|---|---|
| How Much Should a Small Business Website Cost in 2026? | Business Growth | small business website cost 2026 | /pricing, /faq |
| 7 On-Page SEO Basics Every New Website Needs | SEO | on-page SEO basics | /services, /pricing, /contact |
| Designing Websites That Convert International Visitors | Web Design | website conversion international | /privacy, /terms, /pricing, /contact |
| Website Builder vs Web Design Agency: Which Is Right for Your Small Business? | Business Growth | website builder vs agency | /pricing, cost article, /contact |
| How Long Does It Take to Build a Website? A Realistic Timeline | Web Design | how long to build a website | /how-it-works, /contact |
| Do You Really Need a Website in 2026? Why Social Media Isn't Enough | Business Growth | do i need a website | /privacy, /services, /pricing, /contact |
| Website Maintenance: What It Includes and Why It Matters | Web Design | website maintenance | /website-maintenance, /contact |
| 5 Signs It's Time to Redesign Your Website | Web Design | signs to redesign website | /website-redesign, /services, /contact |
| Local SEO Basics: How Small Businesses Show Up on Google Maps | SEO | local SEO basics | /blog (on-page basics), /seo-services, /contact |
| How to Choose the Perfect Domain Name for Your Business | Business Growth | choose a domain name | /pricing, /contact |
| What Is Web Hosting? A Plain-English Guide for Beginners | Business Growth | what is web hosting | /blog (on-page basics), /contact |
| 9 Trust Signals That Turn Website Visitors Into Customers | Web Design | website trust signals | /about, /pricing, /privacy, /contact |
| Why Website Speed Matters (and How to Make Your Site Faster) | SEO | website speed | /website-design, /contact |
| How to Write Website Content That Actually Sells | Marketing | website copywriting | /contact |
| Does Your Business Really Need a Blog? | Marketing | does my business need a blog | /seo-services, /pricing, /contact |
| How to Start Selling Online: An E-Commerce Guide for 2026 | E-Commerce | start selling online | /ecommerce-website-development, /privacy, /contact |
| How Many Pages Does a Small Business Website Need? | Web Design | how many pages website | /pricing, /services, /contact |
| Mobile-First Design: Why Your Website Must Work on Phones | Web Design | mobile-first design | /website-redesign, /contact |
| Website Security Basics Every Small Business Should Know | Web Design | website security basics | /website-maintenance, /contact |
| How to Prepare Your Content Before a Website Build | Web Design | prepare website content | /how-it-works, copywriting post, /contact |
| Google Business Profile: A Step-by-Step Setup Guide | SEO | google business profile setup | local SEO post, /seo-services, /contact |
| 8 Ways to Get More Leads From Your Website | Business Growth | get more leads from website | speed post, /contact |
| Website Accessibility: What It Is and Why It Matters | Web Design | website accessibility | /contact |
| Core Web Vitals Explained Without the Jargon | SEO | core web vitals explained | speed post, /website-design, /contact |

---

## Internal-link architecture (content clusters)

- **Commercial hub:** Home → Pricing → Package pages → Contact (quote funnel).
- **Services cluster (hub-and-spoke):** Services hub → the five service detail pages (Website Design, E-Commerce, Redesign, Maintenance, SEO) → Pricing + supporting blog posts → Contact. Service pages cross-link to each other and are linked site-wide from the footer "Our Services" column.
- **Topical-authority cluster (blog):** cost → builder-vs-agency → timeline → How It Works / FAQ, cross-linked; SEO & conversion posts → Services.
- **Trust cluster:** footer + inner pages → About, FAQ, How It Works, Privacy, Terms.

---

## Structured-data coverage

| Type | Where |
|---|---|
| Organization + WebSite | Every page (site-wide `@graph`) |
| BreadcrumbList | Pages, blog posts, pricing, package pages |
| FAQPage | Home, FAQ, Services, Website Design, E-Commerce, Redesign, SEO, Maintenance (from visible FAQ sections only) |
| BlogPosting | Blog posts |
| Service + Offer | Package pages |
| JobPosting | Career detail pages (`/careers/{slug}`) — remote (TELECOMMUTE) with worldwide applicant requirements |

> No `Review`, `AggregateRating` or `LocalBusiness` schema is emitted — there are no verified reviews and no physical storefront, so adding them would be misleading. Add real review schema only when you have genuine, displayed reviews.

## Global targeting

The site targets **every country** (no geo restriction). `Organization`/`Service` `areaServed` is `Worldwide`, and each page declares `hreflang="x-default"` + `hreflang="en"` on its canonical URL — signalling a single global English site to Google. The inquiry form's country dropdown lists **all countries**. Set a real `APP_URL` (HTTPS domain) before launch so canonical/hreflang/OG URLs are absolute, then submit the sitemap in Google Search Console.
