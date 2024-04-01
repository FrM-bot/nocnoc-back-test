<?php
namespace App\Env;

// class Constants {
//     // env('JWT_SECRET')
//     public const JWT_SECRET = 'JWT_SECRET';
//     public const CLIENT_URL = 'asdsads';
//     // env('CLIENT_URL')
// }

enum Constants: string {
    case JWT_SECRET = 'JWT_SECRET';
    case CLIENT_URL = 'CLIENT_URL';
}