<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckStatusUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->user()){
            if($request->user()->type == 'CUSTOMER'){
                if($request->user()->status != 'ACTIVE'){   
                    $request->user()->currentAccessToken()->delete();
                }
            }
            if($request->user()->type == 'VENDOR'){
                if($request->user()->status == 'BLOCK'){
                    $request->user()->currentAccessToken()->delete();
                }
            }
        }
        return $next($request);
    }
}
