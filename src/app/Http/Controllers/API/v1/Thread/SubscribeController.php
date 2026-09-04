<?php

namespace App\Http\Controllers\API\v1\Thread;

use App\Http\Controllers\Controller;
use App\Models\Subscribe;
use App\Models\Thread;

class SubscribeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'user-block']);
    }

    public function subscribe(Thread $thread)
    {
        Subscribe::firstOrCreate([
            'thread_id' => $thread->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'user subscribed successfully',
        ], 200);
    }

    public function unSubscribe(Thread $thread)
    {
        Subscribe::query()
            ->where('thread_id', $thread->id)
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json([
            'message' => 'user unsubscribed successfully',
        ], 200);
    }
}
