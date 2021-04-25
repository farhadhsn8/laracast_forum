<?php

namespace App\Http\Controllers\API\v01\Channel;

use App\Models\Channel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    public function getAllChannels()
    {
        return response()->json(Channel::all() , 200);
    }


    

    public function createNewChannel(Request $request)
    {
        $request->validate([
            'name'=>['required']
        ]);

            //use repository 

        resolve(ChannelRepository::class)->create($request->name);


        return response()->json([
            'message' =>'channel created successfully'
        ] , 201);
    }
}
