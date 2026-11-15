<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Resolves a visitor's country without any paid service or package:
 *   1. Cloudflare's CF-IPCountry header (instant, free, accurate) when present;
 *   2. otherwise a cached lookup via a free IP geolocation API (per IP, 30 days).
 * Always fails soft to null so visitor tracking never breaks a page.
 */
class GeoLocator
{
    /** @return array{code:?string, name:?string} */
    public function locate(Request $request): array
    {
        $cf = strtoupper(trim((string) $request->header('CF-IPCountry')));
        if (strlen($cf) === 2 && ! in_array($cf, ['XX', 'T1'], true)) {
            return ['code' => $cf, 'name' => $this->name($cf)];
        }

        $ip = (string) $request->ip();
        if ($ip === '' || $this->isPrivate($ip)) {
            return ['code' => null, 'name' => null];
        }

        return Cache::remember('geo:'.$ip, now()->addDays(30), function () use ($ip) {
            try {
                $r = Http::timeout(2)->get("http://ip-api.com/json/{$ip}", ['fields' => 'status,countryCode,country']);
                if ($r->ok() && $r->json('status') === 'success' && $r->json('countryCode')) {
                    return ['code' => $r->json('countryCode'), 'name' => $r->json('country')];
                }
            } catch (\Throwable $e) {
                // ignore — tracking must never fail
            }

            return ['code' => null, 'name' => null];
        });
    }

    public function name(?string $code): ?string
    {
        if (! $code) {
            return null;
        }
        if (class_exists(\Locale::class)) {
            $name = \Locale::getDisplayRegion('-'.$code, 'en');
            if ($name && $name !== $code) {
                return $name;
            }
        }

        return $code;
    }

    private function isPrivate(string $ip): bool
    {
        return ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
}
