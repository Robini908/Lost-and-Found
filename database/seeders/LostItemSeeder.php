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
        // Get users and categories
        $users = User::all();
        $categories = Category::all();

        // Create sample reported items (user reporting their own lost items)
        for ($i = 0; $i < 5; $i++) {
            $lat = $this->faker->latitude;
            $lng = $this->faker->longitude;

            LostItem::create([
                'user_id' => $users->random()->id,
                'title' => $this->faker->sentence,
                'description' => $this->faker->paragraph,
                'category_id' => $categories->random()->id,
                'status' => 'lost',
                'item_type' => LostItem::TYPE_REPORTED,
                'condition' => $this->faker->randomElement(['new', 'like_new', 'excellent', 'good', 'fair', 'poor', 'damaged']),
                'brand' => $this->faker->company,
                'model' => $this->faker->word,
                'color' => $this->faker->safeColorName,
                'serial_number' => $this->faker->bothify('??-####-####'),
                'estimated_value' => $this->faker->randomFloat(2, 10, 1000),
                'currency' => 'USD',
                'location_type' => $this->faker->randomElement(['specific', 'area']),
                'location_address' => $this->faker->address,
                'location_lat' => $lat,
                'location_lng' => $lng,
                'area' => $this->faker->optional()->city,
                'landmarks' => $this->faker->optional()->sentence,
                'date_lost' => Carbon::now()->subDays(rand(1, 30)),
                'is_anonymous' => $this->faker->boolean(20),
                'is_verified' => $this->faker->boolean(20),
                'expires_at' => Carbon::now()->addDays(30),
                'additional_details' => json_encode([
                    'distinguishing_marks' => $this->faker->optional()->sentence,
                    'last_seen' => $this->faker->dateTimeThisMonth()->format('Y-m-d H:i:s'),
                ])
            ]);
        }

        // Create sample searched items (user searching for someone else's lost items)
        for ($i = 0; $i < 3; $i++) {
            $lat = $this->faker->latitude;
            $lng = $this->faker->longitude;

            LostItem::create([
                'user_id' => $users->random()->id,
                'title' => $this->faker->sentence,
                'description' => $this->faker->paragraph,
                'category_id' => $categories->random()->id,
                'status' => 'lost',
                'item_type' => LostItem::TYPE_SEARCHED,
                'condition' => $this->faker->randomElement(['new', 'like_new', 'excellent', 'good', 'fair', 'poor', 'damaged']),
                'brand' => $this->faker->optional()->company,
                'model' => $this->faker->optional()->word,
                'color' => $this->faker->safeColorName,
                'estimated_value' => $this->faker->optional()->randomFloat(2, 10, 1000),
                'currency' => 'USD',
                'location_type' => $this->faker->randomElement(['specific', 'area']),
                'location_address' => $this->faker->address,
                'location_lat' => $lat,
                'location_lng' => $lng,
                'area' => $this->faker->optional()->city,
                'landmarks' => $this->faker->optional()->sentence,
                'date_lost' => Carbon::now()->subDays(rand(1, 30)),
                'is_anonymous' => $this->faker->boolean(20),
                'is_verified' => $this->faker->boolean(20),
                'expires_at' => Carbon::now()->addDays(30),
                'additional_details' => json_encode([
                    'owner_description' => $this->faker->sentence,
                    'last_known_location' => $this->faker->sentence,
                ])
            ]);
        }

        // Create sample found items
        for ($i = 0; $i < 4; $i++) {
            $lat = $this->faker->latitude;
            $lng = $this->faker->longitude;
            $finder = $users->random();

            LostItem::create([
                'user_id' => $finder->id,
                'title' => $this->faker->sentence,
                'description' => $this->faker->paragraph,
                'category_id' => $categories->random()->id,
                'status' => 'found',
                'item_type' => LostItem::TYPE_FOUND,
                'condition' => $this->faker->randomElement(['new', 'like_new', 'excellent', 'good', 'fair', 'poor', 'damaged']),
                'brand' => $this->faker->optional()->company,
                'model' => $this->faker->optional()->word,
                'color' => $this->faker->safeColorName,
                'serial_number' => $this->faker->optional()->bothify('??-####-####'),
                'estimated_value' => $this->faker->optional()->randomFloat(2, 10, 1000),
                'currency' => 'USD',
                'location_type' => $this->faker->randomElement(['specific', 'area']),
                'location_address' => $this->faker->address,
                'location_lat' => $lat,
                'location_lng' => $lng,
                'area' => $this->faker->optional()->city,
                'landmarks' => $this->faker->optional()->sentence,
                'date_found' => Carbon::now()->subDays(rand(1, 30)),
                'found_by' => $finder->id,
                'found_at' => Carbon::now()->subDays(rand(1, 30)),
                'is_anonymous' => $this->faker->boolean(20),
                'is_verified' => $this->faker->boolean(80),
                'expires_at' => Carbon::now()->addDays(30),
                'additional_details' => json_encode([
                    'found_location_details' => $this->faker->sentence,
                    'storage_location' => $this->faker->word,
                    'condition_details' => $this->faker->optional()->sentence,
                ])
            ]);
        }
    }
}
