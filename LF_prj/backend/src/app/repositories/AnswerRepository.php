<?php

namespace App\Repositories;

use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class AnswerRepository
{



    public function getAllAnswers()
    {
        return Answer::query()->latest()->get();
    }

}




