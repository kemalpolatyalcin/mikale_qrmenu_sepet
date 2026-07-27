<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ApiBearerTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Bearer token is missing.'
            ], 401);
        }

        $user = User::where('api_token', $token)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Invalid Bearer token.'
            ], 401);
        }

        auth()->login($user);

        return $next($request);
    }
}
