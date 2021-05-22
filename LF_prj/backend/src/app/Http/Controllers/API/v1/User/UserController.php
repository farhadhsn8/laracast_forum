<?php

namespace App\Http\Controllers\API\v1\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function userNotification(){
        return \response()->json(auth()->user()->unreadNotifications(), 200);
    }

    public function leaderboards(){
        return   resolve(UserRepository::class)->leaderboards();
    }
}
