<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class subscribeController extends Controller
{
    public function subscribe(Thread $thread)
    {
        auth()->user()->subscribes()->create([
            'thread_id' => $thread->id
        ]);
       
        return response()->json([
            'message' => 'user subscribed successfully'
        ] , 200);
    }

    public function unsubscribe(Thread $thread)
    {
        Subscribe::query()->where([
            ['thread_id' , $thread->id] , 
            ['user_id'   , auth()->id()]
        ])->delete();
       
        return response()->json([
            'message' => 'user unsubscribed successfully'
        ] , 200);
    }

}
