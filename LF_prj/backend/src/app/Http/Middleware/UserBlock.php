<?php

namespace App\Http\Middleware;

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
        $isBlock = resolve(UserRepository::class)->isBlock();
        if(! $isBlock){
            return $next($request);
        }
        else{
            return response()->json([
                'message' =>'you arre been blocked'
            ], 403);
        }
    }
}
