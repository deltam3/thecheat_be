<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


use App\Models\Post;
use App\Models\User;
use App\Models\Community;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id, 
            'community_id' => Community::inRandomOrder()->first()->id, 
            'title' => $this->faker->sentence, 
            'content' => $this->faker->paragraph, 
            'view_count' => $this->faker->numberBetween(0, 1000), 
            'is_flagged' => $this->faker->boolean, 
            'profile_image' => $this->faker->imageUrl(300, 300, 'people'), 
            'thumbnail_image' => $this->faker->imageUrl(100, 100, 'nature'), 
            'police_station_name' => $this->faker->company, 
            'victim_site_url' => $this->faker->url, 
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null, 
        ];
    }
}
