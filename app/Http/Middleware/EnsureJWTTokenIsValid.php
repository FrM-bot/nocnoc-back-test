<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use App\Enums\Status;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// class RequestWithJWTPayload extends Request
// {
//     public $payload;
// }
class EnsureJWTTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // return response([
        //     'status' => Status::ERROR,
        //     'error' => $request->bearerToken()
        // ]);
        try {
            //code...
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json([
                    'status' => Status::ERROR,
                    'error' => 'Token not found'
                ], 401);
            }
            $jwtSecretKey = env("JWT_SECRET");

            $decodedToken = JWT::decode($token, new Key($jwtSecretKey, 'HS256'));

            // return response([
            //     'status' => Status::SUCCESS,
            //     'data' => [
            //         'message' => 'pong-middleware',
            //         'payload' => $decoded,
            //         'cookie' => $request->bearerToken(),
            //         'c2' => request()->cookie('accessToken')
            //     ],
            // ], 200);

            // $request->request->set('payload', $decoded);
            $request->session()->put('payload', $decodedToken);

            return $next($request);
        } catch (ValidationException $error) {
            return response([
                'status' => Status::ERROR,
                'error' => 'Token must be a valid JWT'
            ], 404);
        }
    }
}
