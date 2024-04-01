<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Mail\AccountValidationMail;
use App\Mail\InvitationMail;
use Illuminate\Support\Facades\Mail;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use App\Models\Role;
use App\Enums\Status;
use App\Enums\UserType;
use App\Env\Constants;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class SignUp extends Controller
{
    public function signUpAdmin (Request $request) {
        // try sign up validation
        try {
            $request->validate(User::$signUpRules);
        } catch (ValidationException $validationException) {
            return response([
                'status' => Status::ERROR,
                'error' =>  $validationException->validator->errors()->toArray()
            ], 400);
        }

        try {
            ['email' => $email, 'password' => $password, 'clientPassKey' => $clientPassKey] = $request;

            $passKey = env('PASS_KEY');
    
            if ($clientPassKey != $passKey) {
                return response([
                    'status' => Status::ERROR,
                    'message' => 'Invalid pass key.'
                ], 400);
            }
    
            $jwtSecretKey = env('JWT_SECRET');
            
            // expire token after sign in 5 minutes - expressed in seconds
            $exp = time() + (30 * 60);
            
            $hashedPassword = Hash::make($password, [
                'rounds' => 12,
            ]);
    
            $payload = [
                'email' => $email,
                'password' => $hashedPassword,
                'exp' => $exp
            ];
    
            $jwt = JWT::encode($payload, $jwtSecretKey, 'HS256');

            $clientUrl = env('CLIENT_URL');

            Mail::to($email)->send(new AccountValidationMail($email, "$clientUrl/verify/$jwt"));
    
            return response([
                'status' => Status::SUCCESS,
                'data' => [
                    'message' => 'Check your email to verify your account.'
                ],
            ], 200);
        } catch (Exception $error) {
            report($error);
            Log::error($error);
     
            // 'error' => $error->getMessage()
            return response([
                'status' => Status::ERROR,
                'error' => 'Server error.'
            ], 500);
        }
    }

    public function verifyAccount (Request $request) {
        try {
            $request->validate(User::$uniqueEmailRules);
        } catch (ValidationException $validationException) {
            return response([
                'status' => Status::ERROR,
                'error' => $validationException->getMessage()
            ], 400);
        }

        try {
            // get authorization header from request
            $token = $request->bearerToken();

            if (!$token) {
                return response([
                    'error' => 'Token not found.'
                ], 400);
            }

            $jwtSecretKey = env("JWT_SECRET");

            // decode token to get email and password from payload
            $decoded = JWT::decode($token, new Key($jwtSecretKey, 'HS256'));
            $decodeEmail = $decoded->email;
            $decodedPassword = $decoded->password;
            
            // $existsUser = User::where('email', $decodeEmail)->exists();

            // if exists user
            // if ($existsUser) {
            //     return response([
            //         'status' => Status::ERROR,
            //         'error' => 'Email already exists.'
            //     ], 400);
            // }

            ['password' => $password] = $request;

            // id password not exists, return error 400 Bad Request
            if (!$password) {
                return response([
                    'status' => Status::ERROR,
                    'error' => 'No password provided.'
                ], 400);
            }

            // The passwords not match...
            if (!Hash::check($password, $decodedPassword)) {
                return response([
                    'status' => Status::ERROR,
                    'error' => 'Password not match.'
                ], 400);
            }

            // if not exist role, create role
            $role = Role::firstOrCreate(['name' => UserType::ADMIN]);

            // create user with role admin
            User::create([
                'email' => $decodeEmail,
                'password' => $decodedPassword,
                'role_id' => $role->id,
            ]);

            return response([
                'status' => Status::SUCCESS,
                'data' => [
                    'message' => 'Account verified.'
                ],
            ], 200);
        } catch (Exception $error) {
            Log::error($error);
            return response([
                'status' => Status::ERROR,
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function inviteUser (Request $request) {
        try {
            $request->validate(User::$uniqueEmailRules);
        } catch (ValidationException $validationException) {
            return response([
                'status' => Status::ERROR,
                'message' => $validationException->getMessage()
            ], 400);
        }

        try {
            $payload = $request->session()->get('payload');
            // $payloadEmail = $payload->email;
            $role = $payload->role;

            if ($role !== UserType::ADMIN) {
                return response([
                    'status' => Status::ERROR,
                    'error' => 'Not allowed.'
                ], 403);
            }
    
            ['email' => $userEmailToInvite] = $request;
            
            // if exists user
            // $existsUser = User::where('email', $userEmailToInvite)->exists();

            // if exists user
            // if ($existsUser) {
            //     return response([
            //         'status' => Status::ERROR,
            //         'error' => 'This user already exists.'
            //     ], 400);
            // }

            // expire token after sign in 5 minutes - expressed in seconds
            $exp = time() + (30 * 60);
    
            $payload = [
                'email' => $userEmailToInvite,
                'exp' => $exp
            ];
    
            $jwtSecretKey = env('JWT_SECRET');
            // print_r($jwtSecretKey);
    
            $token = JWT::encode($payload, $jwtSecretKey, 'HS256');
    
            $clientUrl = env('CLIENT_URL');

            Mail::to($userEmailToInvite)->send(new InvitationMail("$clientUrl/invitation/$token"));
    
            return response([
                'status' => Status::SUCCESS,
                'data' => [
                    'message' => 'Invitation sent to user successfully.'
                ],
            ], 200);
        } catch (ValidationException $error) {
            report($error);
            Log::error($error);
     
            return response([
                'status' => Status::ERROR,
                'error' => $error->getMessage()
                // 'error' => $error->validator->errors()->toArray()
            ], 500);
        }
    }
    
    public function acceptInvitation (Request $request) {
        // if exists user
        // try {
        //     $request->validate(User::$uniqueEmailRules);
        // } catch (ValidationException $validationException) {
        //     return response([
        //         'status' => Status::ERROR,
        //         'message' => $validationException->getMessage()
        //     ], 400);
        // }

        try {
            // get authorization header from request
            $token = $request->bearerToken();

            ['password' => $password] = $request;

            if (!$token) {
                return response([
                    'error' => 'Token not found.'
                ], 400);
            }

            $jwtSecretKey = env("JWT_SECRET");

            // decode token to get email and password from payload
            $decoded = JWT::decode($token, new Key($jwtSecretKey, 'HS256'));
            $decodeEmail = $decoded->email;

            $existsUser = User::where('email', $decodeEmail)->exists();

            if ($existsUser) {
                return response([
                    'status' => Status::ERROR,
                    'error' => 'Email already exists.'
                ], 400);
            }

            // Hash password
            $hashedPassword = Hash::make($password, [
                'rounds' => 12,
            ]);

            $role = Role::firstOrCreate(['name' => UserType::USER]);

            // Create new user 
            User::create([
                'email' => $decodeEmail,
                'password' => $hashedPassword,
                'role_id' => $role->id,
            ]);

            return response([
                'status' => Status::SUCCESS,
                'data' => [
                    'message' => 'Invitation accepted successfully.'
                ]
            ], 200);
        } catch (Exception $error) {
            report($error);
            Log::error($error);
         
            return response([
                'status' => Status::ERROR,
                'error' => 'Server error.'
            ], 500);
        }
    }
}
