# Webveo — Website & SEO CMS

A full-featured Laravel CMS for an international web-design agency: a dynamic public marketing site, a project-inquiry → lead pipeline, package management, blog, a drag-and-drop page builder, and a complete SEO manager — all controlled from a secure, role-based admin panel.

> **`Webveo` is a placeholder brand name.** Change it (plus contact details, logo, socials) in **Admin → Settings** — it propagates across the whole site, including SEO. See **`SEO_IMPLEMENTATION_REPORT.md`** and **`CONTENT_SEO_MAPPING.md`** for the content/SEO audit and page-by-page mapping.

**Stack:** Laravel 13 · PHP 8.3 · MySQL 8 · Blade · Tailwind v4 + Alpine.js (admin) · a ported custom design system (public) · Vite.

---

## Features

### Public website (dynamic)
- Home, Pricing (with comparison table), Package detail pages, Blog (list/category/tag/post), and CMS pages (About, Services, Industries, How It Works, FAQ, Privacy, Terms, + any admin-created page)
- Service detail pages (topic cluster): Website Design, E-Commerce Development, Website Redesign, Website Maintenance, SEO Services — linked from the Services hub and footer, each with FAQPage schema
- Portfolio / Our Work page (`/portfolio`) — capability- and example-driven, admin-extendable with real case studies; linked from the homepage and nav
- Homepage links straight to the service pages and shows a factual stats band; testimonial slots are admin-managed and render only when genuine reviews exist (no fabricated reviews ship)
- Careers: `/careers` listing + `/careers/{slug}` detail pages with JobPosting schema, managed from the admin
- Project-inquiry form accepts an optional file upload (PDF/Word/Excel/image, stored privately, downloadable by staff)
- Telegram chat button (floating + footer), an admin-editable hero banner image, and a site-wide team-size figure — all managed in Settings / the page builder
- Custom, on-brand 404 page (correct HTTP 404 status) that keeps lost visitors on-site with helpful links
- Global targeting: worldwide `areaServed` schema, `hreflang` x-default/en, and a full-country inquiry form (no geo restriction)
- Project-inquiry funnel: validated form → stored lead with a unique reference → confirmation page → queued confirmation + admin-notification emails
- Nav & footer from the database, per-page SEO (title, meta, canonical, Open Graph, JSON-LD schema)
- Structured data: Organization + WebSite (site-wide), BreadcrumbList, BlogPosting, Service/Offer, and FAQPage (from visible FAQ content)
- Dynamic `sitemap.xml` (de-duplicated), `robots.txt`, and database-driven 301/302 redirects

### Admin panel (`/admin`, role-based)
Dashboard · **Leads/Inquiries** (pipeline, notes, assignment, file attachments, CSV export) · **Packages** (CRUD + features + pricing) · **Careers** (job openings CRUD, published to `/careers`) · **Page Builder** (sections: hero with gradient highlight, features, stats, FAQ, testimonials, CTA, text+image, **rich text / content**, live pricing/blog, custom HTML — drag-to-reorder, duplicate, visibility) · **Blog** (posts, categories, tags, scheduling) · **Media** library · **Navigation** · **SEO Manager** (audit dashboard + redirects + global defaults) · **Settings** · **Users** · **Roles & permissions** · **Activity log**.

---

## Requirements
- PHP 8.3+ (ext: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo, gd, dom)
- Composer 2 · Node 18+ / npm · MySQL 8+

---

## Local setup

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate
# Edit .env: set DB_* credentials and the ADMIN_* values (see below)

# 3. Create the database + user in MySQL
#   CREATE DATABASE webnew_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
#   CREATE USER 'webnew'@'localhost' IDENTIFIED BY 'your-password';
#   GRANT ALL PRIVILEGES ON webnew_cms.* TO 'webnew'@'localhost'; FLUSH PRIVILEGES;

# 4. Migrate + seed (roles, admin, settings, menus, 4 packages, pages, sample blog)
php artisan migrate --seed

# 5. Storage symlink (for uploaded media)
php artisan storage:link

# 6. Build assets + run
npm run build        # or: npm run dev
php artisan serve
```

Visit `http://localhost:8000` (public) and `http://localhost:8000/admin` (admin).

### Admin access
The first admin is created from the `ADMIN_*` values in `.env` during `db:seed`.
Default seeded login: `admin@yourbrand.example` / your `ADMIN_PASSWORD`. **Change the password in-app after first login.**

### Running tests
```bash
php artisan test        # 29 feature tests (in-memory SQLite, no MySQL needed)
```

---

## How-to guides

**Add a package:** Admin → Packages → *Add Package*. Set name, price (or tick *Custom pricing*), specs, included/excluded/add-on features, SEO, and status. Published + active packages appear on the public pricing page automatically.

**Build a page:** Admin → Pages → *New Page* (title, slug, status). Then on the builder, *Add section* (hero, features, pricing, etc.), edit each section's fields, drag the ⠿ handle to reorder, and *Save Page*. Visit `/<slug>` to preview. Note: `home`, `pricing`, `contact`, and `blog` have dedicated templates; the builder drives all other pages.

**Write a blog post:** Admin → Blog → *New Post*. Title, body (HTML — sanitized on save), category (pick or create), comma-separated tags, featured image, status, and publish date (a future date = scheduled). SEO fields optional.

**Edit site identity / SEO defaults / contact / socials:** Admin → Settings (changes flow to the public site via a cache that clears on save).

**Manage navigation:** Admin → Navigation (header + footer menus).

**SEO:** Admin → SEO Manager — review the audit (missing/duplicate titles & descriptions), manage 301/302 redirects, and check sitemap/robots. Global defaults live in Settings.

**Users & roles:** Admin → Users (create admins, assign roles). Admin → Roles (toggle a permission matrix). Roles: Super Admin, Administrator, Content Manager, SEO Manager, Sales Manager.

---

## Production deployment checklist

1. **Environment:** `APP_ENV=production`, `APP_DEBUG=false`, set `APP_URL` to your HTTPS domain, generate `APP_KEY`.
2. **Database:** create the MySQL DB + a dedicated (non-root) user; set `DB_*`. Run `php artisan migrate --force --seed`.
3. **Admin:** set strong `ADMIN_*` before seeding; change the password in-app afterward.
4. **Mail:** set `MAIL_MAILER=smtp` + SMTP credentials so inquiry emails send. Run a queue worker: `php artisan queue:work` (or Supervisor/Horizon) since emails are queued.
5. **Assets:** `npm run build`.
6. **Storage:** `php artisan storage:link`.
7. **HTTPS / cookies:** serve over TLS; set `SESSION_SECURE_COOKIE=true`.
8. **Caches:** `php artisan config:cache route:cache view:cache` (re-run on deploy).
9. **Web root:** point the vhost at `public/`. Ensure `storage/` and `bootstrap/cache/` are writable.
10. **Branding:** replace the `Webveo` placeholder name, logo/favicon (Settings → upload), contact details, socials, and sample blog with your real content. Add real client testimonials (homepage shows them automatically once added — no fabricated reviews ship).

### Security notes (built in)
CSRF on all forms · bcrypt password hashing · role-based authorization (middleware + gates) · Form Request validation · Blade output escaping · **HTML sanitization** of rich content · image-only uploads (SVG excluded) · **rate limiting** on login (5/min) and the inquiry form (6/min) · honeypot on the public form · security headers (X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy) · admin pages `noindex` · secrets in `.env` (never committed).

---

## Project structure (highlights)
```
app/Http/Controllers/Site      Public controllers (home, packages, blog, inquiry, sitemap)
app/Http/Controllers/Admin     Admin modules (dashboard, inquiries, packages, pages, blog, media, menus, seo, settings, users, roles, activity)
app/Http/Middleware            EnsureUserIsAdmin, CheckPermission, HandleRedirects, SecurityHeaders
app/Models                     20 Eloquent models (+ HasSeo trait)
app/Services                   SeoService, InquiryService
app/Support                    SectionTypes (page builder schema), LeadOptions, HtmlSanitizer
app/Mail                       Queued inquiry mailables
database/migrations            21 migrations
database/seeders               RBAC, admin, settings, menus, packages, pages, blog
resources/views/site           Public pages + section partials
resources/views/admin          Admin panel views
resources/views/partials/site  Layout, header, footer, package card, section renderers
routes/web.php                 Public routes
routes/admin.php               Admin routes (prefix /admin, name admin.)
tests/Feature                  29 feature tests
```
