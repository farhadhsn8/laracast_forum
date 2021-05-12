<?php

namespace App\Repositories;

use App\Models\Answer;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class AnswerRepository
{
    public function getAllAnswers()
    {
        return Answer::query()->latest()->get();
    }

    public function store(Request $request)
    {
        Thread::find($request->thread_id)->answers()->create([
            'content'=>$request->input('content'),
            'user_id'=>auth()->id()
        ]);
    }

}




