<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Default fallback values for system settings.
     *
     * @var array<string, mixed>
     */
    public const DEFAULTS = [
        // Store Profile & Receipt
        'store_name' => 'BrewPOS Specialty Coffee',
        'store_tagline' => 'Coffee Shop & Artisan Eatery',
        'store_address' => 'Jl. Boulevard Raya No. 88, Jakarta Selatan',
        'store_phone' => '0812-3456-7890',
        'store_email' => 'hello@brewpos.com',
        'store_logo' => null,
        'receipt_footer' => 'Terima kasih atas kunjungan Anda! Follow IG: @brewpos.id',

        // Tax & Service Charge
        'tax_enabled' => '1',
        'tax_name' => 'PPN',
        'tax_percentage' => '11',
        'service_charge_enabled' => '0',
        'service_charge_percentage' => '5',

        // Payment & QRIS
        'bank_name' => 'BCA',
        'bank_account_number' => '1234567890',
        'bank_account_holder' => 'BrewPOS Indonesia',
        'qris_image' => null,
    ];

    /**
     * Get a setting value by key with cache and default fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $fallback = $default ?? (self::DEFAULTS[$key] ?? null);

        try {
            return Cache::rememberForever("setting.{$key}", function () use ($key, $fallback) {
                try {
                    $record = static::where('key', $key)->first();

                    return $record ? $record->value : $fallback;
                } catch (\Throwable) {
                    return $fallback;
                }
            });
        } catch (\Throwable) {
            return $fallback;
        }
    }

    /**
     * Set / update a setting value.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget("setting.{$key}");
    }

    /**
     * Clear all settings cache.
     */
    public static function clearCache(): void
    {
        foreach (array_keys(self::DEFAULTS) as $key) {
            Cache::forget("setting.{$key}");
        }
    }
}
