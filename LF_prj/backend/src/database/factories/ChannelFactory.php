<?php

namespace Database\Factories;
use Faker\Generator as Faker;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;



$factory->define(Channel::class , function( Faker $faker){
    
    $name =$faker->sentence(4);
    return [
        'name' =>$name,
        'slug'=>\Illuminate\Support\Str::slug($name)

    ];
});

