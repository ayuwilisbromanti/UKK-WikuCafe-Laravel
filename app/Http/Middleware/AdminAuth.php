<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        if(auth('admin_api')->check() && $request->user()->role == "admin"){
            return $next($request);
        }else{
            $message=["message"=>'Anda bukan admin.'];
            return response()->json($message, 401);
        }
    }
}
