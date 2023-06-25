<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ManagerAuth
{
    public function handle(Request $request, Closure $next)
    {
        if(auth('admin_api')->check() && $request->user()->role == "manager"){
            return $next($request);
        }else{
            $message=["message"=>'Anda bukan manager.'];
            return response()->json($message, 401);
        }
    }
}
