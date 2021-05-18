<?php

namespace App\Http\Controllers\API\v1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function userNotification(){
        return \response()->json(auth()->user()->unreadNotifications(), 200);
    }
}
