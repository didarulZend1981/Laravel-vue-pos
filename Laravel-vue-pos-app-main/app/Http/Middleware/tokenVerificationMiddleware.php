<?php

namespace App\Http\Middleware;

use App\Helper\JWTToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class tokenVerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('token');
        $result = JWTToken::verifyToken($token);
        if($result == 'Unauthorized'){
            return to_route('signIn.page');
            // return response()->json(['message' => 'Unauthorized'], 401);
        }else{
            $request->headers->set('email', $result->user_email);
            $request->headers->set('id', $result->user_id);
            return $next($request);
        }
    }
}
