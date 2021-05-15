<?php

namespace App\Http\Controllers\API\v1\Thread;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Thread;
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
        $request->validate([
            'content'=>'required' ,
            'thread_id'=>'required'

        ]);
    
        resolve(AnswerRepository::class)->store($request);

        return \Response()->json([
            'message'=>'answer submitted successfully'
        ] , 200);
    }

    
    public function update(Request $request, Answer $answer)
    {
        $request->validate([
            'content'=>'required' ,

        ]);
        if(Gate::forUser(auth()->user())->allows('user-answer', $answer))
        {
            resolve(AnswerRepository::class)->update($request , $answer);

            return \Response()->json([
                'message'=>'answer updated successfully'
            ] , 200);
        }

        return \Response()->json([
            'message'=>'access denied'
        ] , 403);
    }

  
    public function destroy(Answer $answer)
    {
        if(Gate::forUser(auth()->user())->allows('user-answer', $answer))
        {
            resolve(AnswerRepository::class)->destroy( $answer);


            return \Response()->json([
                'message'=>'answer deleted successfully'
            ] , 200);
        }

        return \Response()->json([
            'message'=>'access denied'
        ] , 403);
    }
}
