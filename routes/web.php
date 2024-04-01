<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignUp;
use App\Http\Controllers\Password;
use App\Http\Controllers\LogIn;
use App\Http\Controllers\Tasks;
use App\Http\Middleware\EnsureJWTTokenIsValid;
// use App\Http\Middleware\Cors;
// use App\Http\Controllers\EmailController;
// use App\Http\Middleware\Cors;
// use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
// use App\Http\Middleware\VerifyCsrfToken;

Route::group([
    'prefix' => 'api'
], function () {
    Route::get('/', function () {
        return view('welcome');
    });
    
    Route::post('/log-in', [LogIn::class, 'logIn']);

    // Route::get('/getToken', [LogIn::class,'getToken']);
    
    Route::post('/sign-up', [SignUp::class, 'signUpAdmin']);

    Route::post('/sign-up', [SignUp::class, 'signUpAdmin']);

    Route::post('/verify', [SignUp::class, 'verifyAccount']);

    Route::post('/forgot-password', [Password::class, 'forgotPassword']);

    Route::patch('/reset-password', [Password::class, 'resetPassword']);

    
    // All routes with auth
    Route::middleware([EnsureJWTTokenIsValid::class])->group(function () {
        Route::post('/invite', [SignUp::class, 'inviteUser']);
        Route::post('/accept-invitation', [SignUp::class, 'acceptInvitation']);
        Route::post('/tasks', [Tasks::class, 'createTask']);
        Route::get('/tasks', [Tasks::class, 'getTasks']);
    });
});
