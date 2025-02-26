<?php

namespace Database\Factories;

use App\Models\LostItemImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class LostItemImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LostItemImage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'lost_item_id' => null, // This will be set in the seeder
            'image_path' => $this->faker->imageUrl(640, 480, 'items', true, 'LostItem', true),
        ];
    }
}