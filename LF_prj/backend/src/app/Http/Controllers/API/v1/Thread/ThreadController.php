<?php

namespace App\Http\Controllers\API\v1\Thread;

use App\Models\Thread;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ThreadController extends Controller
{
    public  function index()
    {
        $threads = resolve(ThreadRepository::class)->getAllAvailableThreads();
        return \response()->json($threads ,200);
    }


    public  function show($slug)
    {
        $thread = resolve(ThreadRepository::class)->getThreadBySlug($slug);
        return \response()->json($thread ,200);
    }
}
