<?php

namespace App\Http\Controllers\API\v1\Thread;

use App\Models\Answer;
use App\Http\Controllers\Controller;
use App\Notifications\NewReplySubmitted;
use App\Repositories\AnswerRepository;
use App\Repositories\SubscribeRepository;
use App\Repositories\UserRepository;
use App\Models\Subscribe;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->middleware(['user-block'])->except([ 'index']);
    }

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


        $notifiable_users_id = resolve(SubscribeRepository::class)->getNotifiableUsers($request->thread_id);
        // Get User Instance From Id
        $notifiable_users = resolve(UserRepository::class)->find($notifiable_users_id);
        // Send NewReplySubmitted Notification To Subscribed Users
        Notification::send($notifiable_users, new NewReplySubmitted(Thread::find($request->thread_id)));

        // Increase User Score
        if (Thread::find($request->input('thread_id'))->user_id !== auth()->id()) {
            $id = Auth::id();
            User::find($id)->increment('score',10);
        }

        // auth()->user()->increment('score' , 10);

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
