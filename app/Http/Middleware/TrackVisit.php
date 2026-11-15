<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use App\Services\GeoLocator;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records a lightweight, privacy-respecting visit for each real public page view
 * (no raw IP stored — only a daily-salted hash for unique counts). Never throws.
 */
class TrackVisit
{
    public function __construct(private GeoLocator $geo) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            if ($this->shouldTrack($request, $response)) {
                $this->record($request);
            }
        } catch (\Throwable $e) {
            // tracking must never affect the response
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $request->ajax()) {
            return false;
        }
        if ($request->is('admin', 'admin/*', 'up', 'sitemap.xml', 'robots.txt', 'thank-you/*')) {
            return false;
        }
        if ($response->getStatusCode() !== 200) {
            return false;
        }
        $type = (string) $response->headers->get('Content-Type');
        if ($type !== '' && ! str_contains($type, 'text/html')) {
            return false;
        }

        return ! $this->isBot((string) $request->userAgent());
    }

    private function record(Request $request): void
    {
        $geo = $this->geo->locate($request);
        $ua = (string) $request->userAgent();

        Visit::create([
            'country' => $geo['code'],
            'country_name' => $geo['name'],
            'path' => Str::limit('/'.ltrim($request->path(), '/'), 500, ''),
            'referrer_host' => $this->referrerHost($request),
            'device' => $this->device($ua),
            'visitor_hash' => hash('sha256', $request->ip().'|'.now()->toDateString().'|'.config('app.key')),
            'created_at' => now(),
        ]);
    }

    private function referrerHost(Request $request): ?string
    {
        $ref = (string) $request->headers->get('referer');
        if ($ref === '') {
            return null;
        }
        $host = parse_url($ref, PHP_URL_HOST) ?: null;

        // Don't record our own domain as a referrer.
        return ($host && $host !== $request->getHost()) ? Str::limit($host, 180, '') : null;
    }

    private function device(string $ua): string
    {
        if (preg_match('/iPad|Tablet|Nexus 7|Nexus 10|Kindle|Silk/i', $ua)) {
            return 'tablet';
        }
        if (preg_match('/Mobile|Android|iPhone|iPod|Windows Phone/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function isBot(string $ua): bool
    {
        return $ua === '' || (bool) preg_match('/bot|crawl|spider|slurp|bing|google|yandex|baidu|duckduck|facebook|embed|preview|monitor|curl|wget|python|java|go-http|headless|lighthouse|pingdom|uptime|semrush|ahrefs|dotbot|mj12/i', $ua);
    }
}
