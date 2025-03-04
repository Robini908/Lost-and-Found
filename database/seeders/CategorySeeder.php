<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'icon' => 'fas fa-laptop',
                'description' => 'Electronic devices and gadgets',
            ],
            [
                'name' => 'Documents',
                'icon' => 'fas fa-file-alt',
                'description' => 'Important documents and papers',
            ],
            [
                'name' => 'Accessories',
                'icon' => 'fas fa-glasses',
                'description' => 'Personal accessories and jewelry',
            ],
            [
                'name' => 'Bags & Wallets',
                'icon' => 'fas fa-briefcase',
                'description' => 'Bags, wallets, and purses',
            ],
            [
                'name' => 'Keys',
                'icon' => 'fas fa-key',
                'description' => 'Keys and keychains',
            ],
            [
                'name' => 'Clothing',
                'icon' => 'fas fa-tshirt',
                'description' => 'Clothing and apparel items',
            ],
            [
                'name' => 'Books',
                'icon' => 'fas fa-book',
                'description' => 'Books and reading materials',
            ],
            [
                'name' => 'Others',
                'icon' => 'fas fa-box',
                'description' => 'Other miscellaneous items',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                [
                    'slug' => Str::slug($category['name']),
                    'icon' => $category['icon'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
