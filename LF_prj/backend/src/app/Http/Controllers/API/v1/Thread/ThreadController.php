<?php

namespace App\Http\Controllers\API\v1\Thread;

use App\Models\Thread;
use App\Http\Controllers\Controller;
use App\Repositories\ThreadRepository;
use Illuminate\Support\Facades\Gate;
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

    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required',
            'content'=>'required',
            'channel_id'=>'required'
        ]);

        resolve(ThreadRepository::class)->store($request);

        return \response()->json([
            'message'=>'thread created successfully'
        ],201);
    }


    public function update(Request $request , Thread $thread)
    {
        if ($request->has('best_answer_id'))
        {
            $request->validate([
                'title'=>'required',
                'content'=>'required',
                'channel_id'=>'required'
            ]);
        }

        if(Gate::forUser(auth()->user())->allows('user-thread', $thread))
        {
            resolve(ThreadRepository::class)->update($thread,$request);
    
            return \response()->json([
                'message'=>'thread updated successfully'
            ],200);
        }

        return \response()->json([
            'message'=>'access denied!'
        ],403);
    }


    public function destroy($id)
    {
        if(Gate::forUser(auth()->user())->allows('user-thread', Thread::find($id)))
        {
            resolve(ThreadRepository::class)->destroy($id);


            return \response()->json([
                'message'=>'thread deleted successfully'
            ],200);
        }

        return \response()->json([
            'message'=>'access denied!'
        ],403);






        

    }
}
