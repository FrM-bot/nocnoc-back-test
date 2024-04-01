<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Enums\Status;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Password extends Controller
{
    public function forgotPassword(Request $request) {
        try {
            ['email' => $email] = $request;

            $existsUser = User::where('email', $email)->exists();

            // if not exists user
            if (!$existsUser) {
                return response([
                    'status' => Status::ERROR,
                    'error' => 'User not found.'
                ], 404);
            }

            $jwtSecretKey = env('JWT_SECRET');

            $exp = time() + (10 * 60);
            
            $payload = [
                'email' => $email,
                'exp' => $exp
            ];
    
            $jwt = JWT::encode($payload, $jwtSecretKey, 'HS256');
            
            $clientUrl = env('CLIENT_URL');
            
            Mail::to($email)->send(new ResetPasswordMail("$clientUrl/reset-password/$jwt"));
    
            return response([
                'status' => Status::SUCCESS,
                'data' => [
                    'message' => 'Check your email to change your password. Url valid for 10 minutes.'
                ],
            ], 200);
        } catch (Exception $error) {
            return response([
                'status' => Status::SUCCESS,
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request) {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response([
                    'status' => Status::ERROR,
                    'error' => 'Token not found.'
                ], 404);
            }

            ['password' => $newPassword] = $request;

            $jwtSecretKey = env('JWT_SECRET');

            $decoded = JWT::decode($token, new Key($jwtSecretKey, 'HS256'));

            $email = $decoded->email;

            $user = User::where('email', $email)->first();

            if (!$user) {
                return response([
                    'status' => Status::ERROR,
                    'error' => 'User not found.'
                ], 404);
            }

            $hashedPassword = Hash::make($newPassword, [
                'rounds' => 12,
            ]);

            $user->password = $hashedPassword;

            $user->save();

            return response([
                'status' => Status::SUCCESS,
                'data' => [
                    'message' => 'Password changed successfully.'
                ],
            ], 200);
        } catch (Exception $error) {
            return response([
                'status' => Status::ERROR,
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
