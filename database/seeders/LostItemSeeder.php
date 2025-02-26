<?php

namespace Database\Seeders;

use App\Models\LostItem;
use App\Models\User;
use App\Models\Category;
use App\Models\LostItemImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Generator as Faker;
use GuzzleHttp\Client;

class LostItemSeeder extends Seeder
{
    /**
     * The Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Create a new seeder instance.
     *
     * @param  \Faker\Generator  $faker
     * @return void
     */
    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure you have some users and categories in the database
        $users = User::all();
        $categories = Category::all();

        // Create sample lost items
        for ($i = 0; $i < 10; $i++) {
            $lostItem = LostItem::firstOrCreate([
                'title' => $this->faker->sentence,
                'description' => $this->faker->paragraph,
                'location' => $this->faker->address,
            ], [
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'status' => 'lost',
                'date_lost' => Carbon::now()->subDays(rand(1, 30)),
                'date_found' => null,
                'found_by' => null,
                'claimed_by' => null,
                'condition' => $this->faker->randomElement(['good', 'fair', 'poor']),
                'value' => rand(10, 1000),
                'item_type' => $this->faker->randomElement(['found', 'reported', 'searched']),
                'is_anonymous' => rand(0, 1),
                'is_verified' => rand(0, 1),
                'expiry_date' => Carbon::now()->addDays(rand(1, 30)),
                'geolocation' => ['lat' => rand(-90, 90), 'lng' => rand(-180, 180)],
                'matched_found_item_id' => null,
            ]);

            // Create images for each lost item using the factory
            LostItemImage::factory()->count(rand(1, 3))->create([
                'lost_item_id' => $lostItem->id,
            ]);
        }

        // Optionally, create some found items
        for ($i = 0; $i < 5; $i++) {
            $foundItem = LostItem::firstOrCreate([
                'title' => $this->faker->sentence,
                'description' => $this->faker->paragraph,
                'location' => $this->faker->address,
            ], [
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'status' => 'found',
                'date_lost' => null,
                'date_found' => Carbon::now()->subDays(rand(1, 30)),
                'found_by' => $users->random()->id,
                'claimed_by' => null,
                'condition' => $this->faker->randomElement(['good', 'fair', 'poor']),
                'value' => rand(10, 1000),
                'item_type' => $this->faker->randomElement(['found', 'reported', 'searched']),
                'is_anonymous' => rand(0, 1),
                'is_verified' => rand(0, 1),
                'expiry_date' => Carbon::now()->addDays(rand(1, 30)),
                'geolocation' => ['lat' => rand(-90, 90), 'lng' => rand(-180, 180)],
                'matched_found_item_id' => null,
            ]);

            // Create images for each found item using the factory
            LostItemImage::factory()->count(rand(1, 3))->create([
                'lost_item_id' => $foundItem->id,
            ]);
        }
    }
}