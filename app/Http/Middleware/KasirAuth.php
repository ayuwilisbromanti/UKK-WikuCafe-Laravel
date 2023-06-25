<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class KasirAuth
{
    public function handle(Request $request, Closure $next)
    {
        if(auth('admin_api')->check() && $request->user()->role == "kasir"){
            return $next($request);
        }else{
            $message=["message"=>'Anda bukan kasir.'];
            return response()->json($message, 401);
        }
    }
}
