<?php

namespace App\Repositories;

use App\Models\Thread;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        Thread::create([
            'title'=>$request->input('title'),
            'slug'=>Str::slug($request->input('title')),
            'content'=>$request->input('content'),
            'channel_id'=>$request->input('channel_id'),
            'user_id'=>auth()->user()->id,
            ]);
    }


    public function update(Thread $thread , Request $request)
    {
        

        if ($request->has('best_answer_id'))
        {
            $thread->update([
                'best_answer_id'=> $request->input('best_answer_id')
            ]);
        }else
        {
            $thread->update([
                'title'=>$request->input('title'),
                'slug'=>Str::slug($request->input('title')),
                'content'=>$request->input('content'),
                'channel_id'=>$request->input('channel_id'),
                
                ]);
        }
    }



    public function destroy($id)
    {
        Thread::destroy($id);      
    }





}




