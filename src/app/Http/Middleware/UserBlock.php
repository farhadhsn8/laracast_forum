<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;

class UserBlock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (resolve(UserRepository::class)->isBlock()) {
            return response()->json([
                'message' => 'you have been blocked',
            ], 403);
        }

        return $next($request);
    }
}
