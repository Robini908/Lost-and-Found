<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Usernotnull\Toast\Concerns\WireToast;

class Settings extends Component
{
    use WireToast;

    public $settings = [];
    protected $settingsService;

    public function boot(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function mount()
    {
        // Simple admin check - we'll assume the middleware or route protection handles proper access control
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

        $this->loadSettings();
    }

    protected function loadSettings()
    {
        $settings = $this->settingsService->all();

        $this->settings = [
            // General settings
            'site_name' => $settings['site_name'] ?? config('app.name'),
            'site_description' => $settings['site_description'] ?? 'Lost and Found Management System',
            'currency' => $settings['currency'] ?? 'USD',
            'timezone' => $settings['timezone'] ?? config('app.timezone'),
            'date_format' => $settings['date_format'] ?? 'Y-m-d',
            'time_format' => $settings['time_format'] ?? 'H:i',
            'items_per_page' => $settings['items_per_page'] ?? '10',
            'maintenance_mode' => $settings['maintenance_mode'] ?? false,

            // Contact & Support
            'contact_email' => $settings['contact_email'] ?? config('mail.from.address'),
            'support_phone' => $settings['support_phone'] ?? '',
            'office_address' => $settings['office_address'] ?? '',
            'office_hours' => $settings['office_hours'] ?? '9:00 AM - 5:00 PM',

            // Security settings
            'max_login_attempts' => $settings['max_login_attempts'] ?? '5',
            'lockout_duration' => $settings['lockout_duration'] ?? '15',
            'password_expires_days' => $settings['password_expires_days'] ?? '90',
            'require_2fa' => $settings['require_2fa'] ?? false,
            'session_lifetime' => $settings['session_lifetime'] ?? '120',
            'enable_recaptcha' => $settings['enable_recaptcha'] ?? false,
            'allowed_file_types' => $settings['allowed_file_types'] ?? 'jpg,jpeg,png,pdf',
            'max_file_size' => $settings['max_file_size'] ?? '5',

            // Notification settings
            'enable_email_notifications' => $settings['enable_email_notifications'] ?? true,
            'enable_sms_notifications' => $settings['enable_sms_notifications'] ?? false,
            'notify_on_item_match' => $settings['notify_on_item_match'] ?? true,
            'notify_admins_on_new_item' => $settings['notify_admins_on_new_item'] ?? true,

            // Reward settings
            'enable_rewards' => $settings['enable_rewards'] ?? true,
            'points_conversion_rate' => $settings['points_conversion_rate'] ?? '0.01',
            'currency_symbol' => $settings['currency_symbol'] ?? '$',
            'min_points_convert' => $settings['min_points_convert'] ?? '1000',
            'reward_points_expiry_days' => $settings['reward_points_expiry_days'] ?? '365',
            'found_item_reward_points' => $settings['found_item_reward_points'] ?? '100',
        ];

        Log::info('Settings loaded', $this->settings);
    }

    public function saveSettings()
    {
        // Simple admin check - we'll assume the middleware or route protection handles proper access control
        if (!Auth::check()) {
            toast()->danger('Unauthorized action')->push();
            return;
        }

        try {
            $this->settingsService->setMany($this->settings);
            $this->settingsService->applyGlobalSettings();

            Log::info('Settings saved and applied globally', $this->settings);
            toast()->success('Settings saved and applied successfully!')->push();
        } catch (\Exception $e) {
            Log::error('Failed to save settings: ' . $e->getMessage());
            toast()->danger('Failed to save settings: ' . $e->getMessage())->push();
        }
    }

    public function resetToDefault()
    {
        if (!Auth::check()) {
            toast()->danger('Unauthorized action')->push();
            return;
        }

        try {
            $this->settingsService->resetToDefault();
            $this->loadSettings(); // Reload the settings in the component
            
            toast()->success('Settings have been reset to default values!')->push();
            
            // Dispatch browser event for UI feedback
            $this->dispatch('settings-reset');
        } catch (\Exception $e) {
            Log::error('Failed to reset settings: ' . $e->getMessage());
            toast()->danger('Failed to reset settings: ' . $e->getMessage())->push();
        }
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
