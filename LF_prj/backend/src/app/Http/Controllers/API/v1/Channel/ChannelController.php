<?php

namespace App\Http\Controllers\API\v1\Channel;

use App\Models\Channel;
use App\Http\Controllers\Controller;
use App\Repositories\ChannelRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    public function getAllChannels()
    {
        $allChannels = resolve(ChannelRepository::class)->all();
        return response()->json( $allChannels, 200);
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




    public function editChannel(Request $request)
    {
        $request->validate([
            'name'=>['required']
        ]);

        resolve(ChannelRepository::class)->update($request->id,$request->name);


        return response()->json([
            'message' =>'channel edited successfully'
        ] , 201);
    }


    public function deleteChannel(Request $request)
    {
        $request->validate([
            'id'=>['required']
        ]);

        resolve(ChannelRepository::class)->delete($request->id);


        return response()->json([
            'message' => 'channel deleted successfully'
        ],200);
    }
}
