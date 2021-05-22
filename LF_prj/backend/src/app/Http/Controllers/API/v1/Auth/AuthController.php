<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use app\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{


    public function register(Request $request)
    {
        $request->validate([
            'name'=>['required'],
            'email'=>['required','email','unique:users'],
            'password'=>['required'],
        ]);
        
        $user = resolve(UserRepository::class)->create($request);
      
      
        $defaultSuperAdminEmail=config('permission.default_super_admin_email');
        $user->email ===$defaultSuperAdminEmail ? $user->assignRole('Super Admin') :  $user->assignRole('User');
        
        return response()->json([
            "message" => "create successfully completed"
        ],200);
    }




    public function login(Request $request)
    {
        $request->validate([
            'email'=>['required','email'],
            'password'=>['required'],
        ]);

        if(Auth::attempt($request->only(['email' , 'password'])))
        {
            return response()->json(Auth::user() , 200);
        }

        throw ValidationException::withMessages([
            'email'=>'incorrect credentials'
            ]);
    }



    public function user()
    {
        $data=[
            Auth::user(),
            'notification' => Auth::user()->unreadNotifications(),

        ];
        return response()->json($data , 200);
    }

    public function logout()
    {
        Auth::logout();

        return response()->json([
            "message" => "logout successfully completed"
        ],200);
    }


   

}
