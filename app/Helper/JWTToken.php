<?php

namespace App\Helper;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken
{
    //token creation
    public static function createToken($user_email, $user_id)
    {
        $key = config('app.jwt_key');
        if (!is_string($key) || empty($key)) {
            throw new Exception('JWT_KEY is not properly set or is not a string.');
        } else {
            $payload = [
                'iss' => 'laravel_vue_pos_application',
                'iat' => time(),
                'exp' => time() + 60 * 60 * 24 * 7,
                'user_email' => $user_email,
                'user_id' => $user_id,
            ];
        }

        $token = JWT::encode($payload, $key, 'HS256');
        return $token;
    }

    //create token for reset new password
    public static function createTokenForResetPassword($user_email)
    {
        $key = config('app.jwt_key');
        if (!is_string($key) || empty($key)) {
            throw new Exception('JWT_KEY is not properly set or is not a string.');
        } else {
            $payload = [
                'iss' => 'laravel_vue_pos_application',
                'iat' => time(),
                'exp' => time() + 60 * 5,
                'user_email' => $user_email,
                'user_id' => '0'
            ];
        }

        $token = JWT::encode($payload, $key, 'HS256');
        return $token;
    }

    //token virify
    public static function verifyToken($token)
    {
        try {
            if (empty($token)) {
                return 'Unauthorized';
            } else {
                $key = config('app.jwt_key');
                if (!is_string($key) || empty($key)) {
                    throw new Exception('JWT_KEY is not properly set or is not a string.');
                } else {
                    $decoded = JWT::decode($token, new Key($key, 'HS256'));
                    return $decoded;
                }
            }
        } catch (Exception) {
            return 'Unauthorized';
        }
    }
}
