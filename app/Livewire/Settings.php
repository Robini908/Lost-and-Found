<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Usernotnull\Toast\Concerns\WireToast;

class Settings extends Component
{
    use WireToast;

    public $settings = [];

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
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        $this->settings = [
            // General settings
            'site_name' => $settings['site_name'] ?? config('app.name'),
            'currency' => $settings['currency'] ?? 'USD',

            // Reward settings
            'enable_rewards' => $settings['enable_rewards'] ?? 'true',
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
            foreach ($this->settings as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'group' => $this->getSettingGroup($key)
                    ]
                );

                // Clear the cache for this setting
                Cache::forget("setting.{$key}");
            }

            Log::info('Settings saved', $this->settings);
            toast()->success('Settings saved successfully!')->push();
        } catch (\Exception $e) {
            Log::error('Failed to save settings: ' . $e->getMessage());
            toast()->danger('Failed to save settings: ' . $e->getMessage())->push();
        }
    }

    protected function getSettingGroup($key)
    {
        $groups = [
            // General settings
            'site_name' => 'general',
            'currency' => 'general',

            // Reward settings
            'enable_rewards' => 'rewards',
            'points_conversion_rate' => 'rewards',
            'currency_symbol' => 'rewards',
            'min_points_convert' => 'rewards',
            'reward_points_expiry_days' => 'rewards',
            'found_item_reward_points' => 'rewards',
        ];

        return $groups[$key] ?? 'general';
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
