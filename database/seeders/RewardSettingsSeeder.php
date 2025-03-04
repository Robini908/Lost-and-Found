<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class RewardSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key' => 'points_conversion_rate',
                'value' => '100',
                'type' => 'number',
                'group' => 'rewards',
                'description' => 'Number of points required for 1 unit of currency'
            ],
            [
                'key' => 'currency_symbol',
                'value' => '$',
                'type' => 'string',
                'group' => 'rewards',
                'description' => 'Currency symbol for rewards'
            ],
            [
                'key' => 'min_points_convert',
                'value' => '1000',
                'type' => 'number',
                'group' => 'rewards',
                'description' => 'Minimum points required for conversion'
            ],
            [
                'key' => 'enable_rewards',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'rewards',
                'description' => 'Enable/disable reward system'
            ]
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
} 