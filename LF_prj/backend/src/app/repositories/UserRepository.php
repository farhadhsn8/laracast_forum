<?php


namespace App\Repositories;
use app\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserRepository 
{
    public function create(Request $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password) ,
        ]);
    }
}