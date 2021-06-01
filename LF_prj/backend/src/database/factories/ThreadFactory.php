<?php

namespace Database\Factories;

use App\Models\Thread;
use App\Models\User;
use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'content' => $faker->realText(),
            'user_id' => \factory(User::class)->create()->id,
            'channel_id' => \factory(Channel::class)->create()->id,
        ];
    }
}
