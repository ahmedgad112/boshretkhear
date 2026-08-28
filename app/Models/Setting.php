<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('app_settings', 300, function () {
            return static::query()->pluck('value', 'key')->all();
        });

        return $settings[$key] ?? $default;
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('app_settings');
    }

    public static function allValues(): array
    {
        return Cache::remember('app_settings', 300, function () {
            return static::query()->pluck('value', 'key')->all();
        });
    }
}
