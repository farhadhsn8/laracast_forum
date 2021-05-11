<?php

namespace App\Http\Controllers\API\v1\Thread;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $answer = resolve(AnswerRepository::class)->getAllAnswers();

        return Response()->json($answer , 200);
    }

   
    public function store(Request $request)
    {
        //
    }

    
    public function update(Request $request, Answer $answer)
    {
        //
    }

  
    public function destroy(Answer $answer)
    {
        //
    }
}
