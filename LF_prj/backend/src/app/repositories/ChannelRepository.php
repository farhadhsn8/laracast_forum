<?php

namespace App\Repositories;

use App\Models\Channel;
use Illuminate\Support\Str;

class ChannelRepository
{
    public function create($name): void
    {
        Channel::create([
            'name' => $name,
            'slug' => Str::slug($name),

        ]);
    }
}