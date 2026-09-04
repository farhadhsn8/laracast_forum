<?php

namespace App\Http\Controllers\API\v1\Thread;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Thread;
use App\Models\User;
use App\Notifications\NewReplySubmitted;
use App\Repositories\AnswerRepository;
use App\Repositories\SubscribeRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class AnswerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'user-block'])->except(['index']);
    }

    public function index()
    {
        $answers = resolve(AnswerRepository::class)->getAllAnswers();

        return response()->json($answers, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => ['required'],
            'thread_id' => ['required', 'exists:threads,id'],
        ]);

        $thread = Thread::find($request->input('thread_id'));

        resolve(AnswerRepository::class)->store($request);

        // Notify the subscribed users about the new reply.
        $notifiableUserIds = resolve(SubscribeRepository::class)->getNotifiableUsers($thread->id);
        $notifiableUsers = resolve(UserRepository::class)->find($notifiableUserIds);
        Notification::send($notifiableUsers, new NewReplySubmitted($thread));

        // Reward the answerer, unless they are replying to their own thread.
        if ($thread->user_id !== auth()->id()) {
            User::find(auth()->id())->increment('score', 10);
        }

        return response()->json([
            'message' => 'answer submitted successfully',
        ], 200);
    }

    public function update(Request $request, Answer $answer)
    {
        $request->validate([
            'content' => ['required'],
        ]);

        if (Gate::forUser(auth()->user())->allows('user-answer', $answer)) {
            resolve(AnswerRepository::class)->update($request, $answer);

            return response()->json([
                'message' => 'answer updated successfully',
            ], 200);
        }

        return response()->json([
            'message' => 'access denied',
        ], 403);
    }

    public function destroy(Answer $answer)
    {
        if (Gate::forUser(auth()->user())->allows('user-answer', $answer)) {
            resolve(AnswerRepository::class)->destroy($answer);

            return response()->json([
                'message' => 'answer deleted successfully',
            ], 200);
        }

        return response()->json([
            'message' => 'access denied',
        ], 403);
    }
}
