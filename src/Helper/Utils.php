<?php declare(strict_types = 1);

namespace App\Helper;

use Firebase\JWT\JWT;
use Exception;
class Utils 
{
    public static function error_message(string $message) : array 
    {
        return [
            'message' => 'failure',
            'body' => [
                'error_msg' => $message
            ]
        ];
    }

    public static function verify_atoken(string $auth_header) : array
    {
        if(!isset($auth_header))
        {
            return Utils::error_message('No Authorization header.');
        }
        $auth_header_values = explode(' ', $auth_header);

        if($auth_header_values < 2)
        {
            return Utils::error_message('No access token.');
        }

        if($auth_header_values[0] !== 'Bearer')
        {
            return Utils::error_message('Invalid Authorization header.');
        }

        $access_token = $auth_header_values[1];

        // Verify access token
        try 
        {
            $jwt = JWT::decode($access_token, $_ENV['APP_KEY'], ['HS256']);
            return ['message' => 'success', 'jwt' => $jwt];
        } 
        catch (Exception $ex)
        {
            return Utils::error_message('Invalid access token: '.$ex->getMessage());
        }
    }
}