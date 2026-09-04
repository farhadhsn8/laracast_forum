<?php

namespace App\Http\Controllers\API\v1\Channel;

use App\Http\Controllers\Controller;
use App\Repositories\ChannelRepository;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function getAllChannelsList()
    {
        $channels = resolve(ChannelRepository::class)->all();

        return response()->json($channels, 200);
    }

    public function createNewChannel(Request $request)
    {
        $request->validate([
            'name' => ['required'],
        ]);

        resolve(ChannelRepository::class)->create($request->name);

        return response()->json([
            'message' => 'channel created successfully',
        ], 201);
    }

    public function updateChannel(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:channels,id'],
            'name' => ['required'],
        ]);

        resolve(ChannelRepository::class)->update($request->id, $request->name);

        return response()->json([
            'message' => 'channel updated successfully',
        ], 200);
    }

    public function deleteChannel(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:channels,id'],
        ]);

        resolve(ChannelRepository::class)->delete($request->id);

        return response()->json([
            'message' => 'channel deleted successfully',
        ], 200);
    }
}
