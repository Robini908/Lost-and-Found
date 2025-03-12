<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type', 'description'];

    /**
     * Cast value to appropriate type based on key
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes) {
                $key = $attributes['key'] ?? '';
                
                // Boolean settings
                if (in_array($key, [
                    'maintenance_mode',
                    'require_2fa',
                    'enable_recaptcha',
                    'enable_email_notifications',
                    'enable_sms_notifications',
                    'notify_on_item_match',
                    'notify_admins_on_new_item',
                    'enable_rewards'
                ])) {
                    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }

                // Numeric settings
                if (in_array($key, [
                    'items_per_page',
                    'max_login_attempts',
                    'lockout_duration',
                    'password_expires_days',
                    'session_lifetime',
                    'max_file_size',
                    'points_conversion_rate',
                    'min_points_convert',
                    'reward_points_expiry_days',
                    'found_item_reward_points'
                ])) {
                    return is_numeric($value) ? (float) $value : $value;
                }

                return $value;
            },
            set: fn ($value) => is_array($value) ? json_encode($value) : $value
        );
    }

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("setting.{$key}");
        return $setting;
    }
}
