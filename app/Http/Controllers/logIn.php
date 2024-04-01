<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Enums\Status;
use App\Env\EnvVars;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Role;

class LogIn extends Controller
{
    public function logIn (Request $request){
        // try validation log in
        try {
            $request->validate(User::$logInRules);
        } catch (ValidationException $validationException) {
            return response([
                'status' => Status::ERROR,
                'error' => $validationException->getMessage()
            ], 500);
        }

        try {
            
            ['email' => $email, 'password' => $password] = $request;

            // get user by email from db
            $user = User::where('email', $email)->select('email', 'password', 'role_id')->first();

            
            // check user exist and password is correct
            if(!$user || !Hash::check($password, $user->password)){
                return response([
                    'status' => Status::ERROR,
                    'error' => 'Wrong email or password.'
                ], 404);
            }

            $role = Role::where('id', $user->role_id)->select('name')->first();

            // create jwt payload token
            $minutes = 60 * 24 * 7; // 60 minutes * 24 hours * 7 days = 7 days
            $exp = time() + ($minutes * 60); // current time + 7 days in seconds - (60 seconds) * (60 minutes * 24 hours * 7 days) = 604800 seconds 
            $payload = [
                'email' => $user->email,
                'role' => $role->name,
                'exp' => $exp
            ];

            $jwtSecretKey = env('JWT_SECRET');


            $accessToken = JWT::encode($payload, $jwtSecretKey, 'HS256'); 

            return response([
                'status' => Status::SUCCESS,
                'data' => [
                    'message' => 'Logged in successfully',
                    'accessToken' => $accessToken,
                    'expires' => $exp
                ],
            ]);
        } catch (Exception $error) {
            Log::error($error);
            // ['errors' => $e->validator->errors()->toArray()]
            return response([
                'status' => Status::ERROR,
                'error' => 'Server error'
                // 'error' => $error->getMessage()
            ], 500);
        }
    }
    public function getToken(){
        return response()->json(['token' => csrf_token()]);
    }
}
