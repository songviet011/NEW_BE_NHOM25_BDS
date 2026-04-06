<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */


    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('sanctum')->user();
        if ($user && $user instanceof \App\Models\Admin) {
            return $next($request);
        }
        return response()->json([
            'message' => 'Bạn cần đăng nhập để thực hiện chức năng này'
        ], 401);
    }
}
// $user = Auth::guard('sanctum')->user();

        // // nếu không có session → dùng Sanctum token
        // if (!$user) {
        //     $token = $request->bearerToken();

        //     if ($token) {
        //         $accessToken = PersonalAccessToken::findToken($token);

        //         if ($accessToken && $accessToken->tokenable instanceof \App\Models\Admin) {
        //             $user = $accessToken->tokenable;
        //         }
        //     }
        // }

        // if (!$user) {
        //     return response()->json([
        //         'message' => 'Bạn cần đăng nhập để thực hiện chức năng này'
        //     ], 401);
        // }

        // if (!($user instanceof \App\Models\Admin)) {
        //     return response()->json([
        //         'message' => 'Bạn cần đăng nhập Admin tai khoan'
        //     ], 401);
        // }

        // // set lại user cho guard admin
        // Auth::guard('sanctum')->setUser($user);

        // return $next($request);