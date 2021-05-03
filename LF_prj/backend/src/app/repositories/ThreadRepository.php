<?php

namespace App\Repositories;

use App\Models\Thread;
use Illuminate\Support\Str;

class ThreadRepository
{
    public function getAllAvailableThreads()
    {
        return Thread::where('flag',1)->latest()->get();
    }

    public function getThreadBySlug($slug) 
    {
        return Thread::where('slug',$slug)->where('flag',1)->first();
    }


}




