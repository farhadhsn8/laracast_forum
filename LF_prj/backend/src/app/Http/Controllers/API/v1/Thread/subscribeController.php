<?php

namespace App\Http\Controllers\API\v1\Thread;

use App\Models\Thread;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;


class subscribeController extends Controller
{
    public function __construct(){
        $this->middleware(['user-block']);
    }

    public function subscribe(Thread $thread)
    {
        Subscribe::query()->where([
            ['thread_id' , $thread->id] , 
            ['user_id'   , auth()->id()]
        ])->create([
            'thread_id' => $thread->id
        ]);
       
        return response()->json([
            'message' => 'user subscribed successfully'
        ] , 200);
    }
    public function unSubscribe(Thread $thread)
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
