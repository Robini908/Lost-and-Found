<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class SettingsService
{
    protected const CACHE_KEY = 'app_settings';
    protected const CACHE_TTL = 3600; // 1 hour

    /**
     * Get default settings with their groups
     */
    protected function getDefaultSettings(): array
    {
        return [
            // General settings
            'site_name' => [
                'value' => config('app.name'),
                'group' => 'general'
            ],
            'site_description' => [
                'value' => 'Lost and Found Management System',
                'group' => 'general'
            ],
            'currency' => [
                'value' => 'USD',
                'group' => 'general'
            ],
            'timezone' => [
                'value' => config('app.timezone'),
                'group' => 'general'
            ],
            'date_format' => [
                'value' => 'Y-m-d',
                'group' => 'general'
            ],
            'time_format' => [
                'value' => 'H:i',
                'group' => 'general'
            ],
            'items_per_page' => [
                'value' => '10',
                'group' => 'general'
            ],
            'maintenance_mode' => [
                'value' => false,
                'group' => 'general'
            ],

            // Contact & Support
            'contact_email' => [
                'value' => config('mail.from.address'),
                'group' => 'contact'
            ],
            'support_phone' => [
                'value' => '',
                'group' => 'contact'
            ],
            'office_address' => [
                'value' => '',
                'group' => 'contact'
            ],
            'office_hours' => [
                'value' => '9:00 AM - 5:00 PM',
                'group' => 'contact'
            ],

            // Security settings
            'max_login_attempts' => [
                'value' => '5',
                'group' => 'security'
            ],
            'lockout_duration' => [
                'value' => '15',
                'group' => 'security'
            ],
            'password_expires_days' => [
                'value' => '90',
                'group' => 'security'
            ],
            'require_2fa' => [
                'value' => false,
                'group' => 'security'
            ],
            'session_lifetime' => [
                'value' => '120',
                'group' => 'security'
            ],
            'enable_recaptcha' => [
                'value' => false,
                'group' => 'security'
            ],
            'allowed_file_types' => [
                'value' => 'jpg,jpeg,png,pdf',
                'group' => 'security'
            ],
            'max_file_size' => [
                'value' => '5',
                'group' => 'security'
            ],

            // Notification settings
            'enable_email_notifications' => [
                'value' => true,
                'group' => 'notifications'
            ],
            'enable_sms_notifications' => [
                'value' => false,
                'group' => 'notifications'
            ],
            'notify_on_item_match' => [
                'value' => true,
                'group' => 'notifications'
            ],
            'notify_admins_on_new_item' => [
                'value' => true,
                'group' => 'notifications'
            ],

            // Reward settings
            'enable_rewards' => [
                'value' => true,
                'group' => 'rewards'
            ],
            'points_conversion_rate' => [
                'value' => '0.01',
                'group' => 'rewards'
            ],
            'currency_symbol' => [
                'value' => '$',
                'group' => 'rewards'
            ],
            'min_points_convert' => [
                'value' => '1000',
                'group' => 'rewards'
            ],
            'reward_points_expiry_days' => [
                'value' => '365',
                'group' => 'rewards'
            ],
            'found_item_reward_points' => [
                'value' => '100',
                'group' => 'rewards'
            ],
        ];
    }

    /**
     * Reset all settings to default values
     */
    public function resetToDefault(): void
    {
        try {
            $defaultSettings = $this->getDefaultSettings();
            
            // Delete all existing settings
            Setting::truncate();
            
            // Insert default settings
            foreach ($defaultSettings as $key => $setting) {
                Setting::create([
                    'key' => $key,
                    'value' => $setting['value'],
                    'group' => $setting['group']
                ]);
            }

            // Clear the cache
            Cache::forget(self::CACHE_KEY);
            
            // Apply the default settings
            $this->applyGlobalSettings();

            Log::info('Settings reset to default values');
        } catch (\Exception $e) {
            Log::error('Failed to reset settings: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all settings with caching
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get a specific setting
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();
        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value
     */
    public function set(string $key, mixed $value, string $group = 'general'): void
    {
        try {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );

            // Clear the cache
            Cache::forget(self::CACHE_KEY);

            Log::info("Setting updated: {$key}", ['value' => $value, 'group' => $group]);
        } catch (\Exception $e) {
            Log::error("Failed to update setting: {$key}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Set multiple settings at once
     */
    public function setMany(array $settings): void
    {
        try {
            foreach ($settings as $key => $value) {
                if (is_array($value)) {
                    $this->set($key, $value['value'], $value['group'] ?? 'general');
                } else {
                    $this->set($key, $value);
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to update multiple settings", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Apply settings globally
     */
    public function applyGlobalSettings(): void
    {
        $settings = $this->all();

        // Apply timezone
        if (isset($settings['timezone'])) {
            date_default_timezone_set($settings['timezone']);
        }

        // Apply session lifetime
        if (isset($settings['session_lifetime'])) {
            config(['session.lifetime' => (int) $settings['session_lifetime']]);
        }

        // Apply file upload settings
        if (isset($settings['max_file_size'])) {
            config(['filesystems.max_file_size' => (int) $settings['max_file_size']]);
        }

        // Apply mail settings
        if (isset($settings['contact_email'])) {
            config(['mail.from.address' => $settings['contact_email']]);
        }

        // Apply maintenance mode
        if (isset($settings['maintenance_mode']) && $settings['maintenance_mode']) {
            if (!app()->isDownForMaintenance()) {
                Artisan::call('down');
            }
        } else {
            if (app()->isDownForMaintenance()) {
                Artisan::call('up');
            }
        }

        // Apply security settings
        if (isset($settings['max_login_attempts'])) {
            config(['auth.max_attempts' => (int) $settings['max_login_attempts']]);
        }

        if (isset($settings['lockout_duration'])) {
            config(['auth.lockout_minutes' => (int) $settings['lockout_duration']]);
        }

        // Apply notification settings
        if (isset($settings['enable_email_notifications'])) {
            config(['notifications.channels.mail.enabled' => (bool) $settings['enable_email_notifications']]);
        }

        if (isset($settings['enable_sms_notifications'])) {
            config(['notifications.channels.sms.enabled' => (bool) $settings['enable_sms_notifications']]);
        }
    }
} 