<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'description', 'type', 'group', 'is_secret'];

    protected $casts = ['is_secret' => 'boolean'];

    /**
     * Get a setting value by key (cached for 10 minutes).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::remember("setting.{$key}", 600, function () use ($key, $default) {
                $val = static::where('key', $key)->value('value');
                return $val !== null ? $val : $default;
            });
        } catch (\Throwable) {
            return $default;
        }
    }

    /**
     * Set a setting value and clear cache.
     */
    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
        Cache::forget("setting.{$key}");
    }
}
