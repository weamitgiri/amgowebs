<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type'];

    public const CACHE_KEY = 'settings.all';

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /**
     * All settings as a key => value map (cached).
     *
     * @return array<string, mixed>
     */
    public static function allSettings(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::query()->get()->mapWithKeys(fn (Setting $s) => [
                $s->key => $s->type === 'boolean' ? (bool) $s->value : $s->value,
            ])->all();
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::allSettings()[$key] ?? $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'text'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? (int) $value : $value, 'group' => $group, 'type' => $type],
        );
    }
}
