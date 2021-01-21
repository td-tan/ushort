<?php declare(strict_types = 1);

namespace App\Controller;

use Helper\RequestData;
use Firebase\JWT\JWT;
use App\Model\User;
use Exception;

class ApiController
{
    public function login(RequestData $rd) : string
    {
        header('Content-Type: application/json');
        // TODO Generalize error message, should not be verbose
        $user = User::query()->where('email', '=', $rd->body['username']);

        // Guard: First verify existence
        if ($user->count() < 1)
        {
            $response = [
                'message' => 'failure',
                'body' => [
                    'error_msg' => 'User does not exists.'
                ]
            ];
            return json_encode($response);
        }

        $user = $user->first();

        // Guard: Verify password hash
        if (!password_verify($rd->body['password'], $user->password))
        {
            $response = [
                'message' => 'failure',
                'body' => [
                    'error_msg' => 'Password is wrong.'
                ]
            ];
            return json_encode($response);
        }

        $payload = array(
            "iss" => "http://ushort.test",
            "aud" => "http://ushort.test",
            "sub" => $user->id,
            "name" => $user->email,
            "iat" => time(),
            "nbf" => time(),
            "exp" => time() + 3600 // Expire after 1 hour
        );

        $jwt = JWT::encode($payload, $_ENV['APP_KEY']);

        $response = [
            'message' => 'success',
            'body' => [
                'access_token' => $jwt
            ]
        ];
        return json_encode($response);
    }

    public function get_user(RequestData $rd) : string
    {
        header('Content-Type: application/json');
        // TODO Implement user data api
        if(!isset($_SERVER['HTTP_AUTHORIZATION']))
        {
            $response = [
                'message' => 'failure',
                'body' => [
                    'error_msg' => 'No Authorization header.'
                ]
            ];
            return json_encode($response);
        }
        $auth_header_values = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);

        if($auth_header_values < 2)
        {
            $response = [
                'message' => 'failure',
                'body' => [
                    'error_msg' => 'No access token.'
                ]
            ];
            return json_encode($response);
        }

        if($auth_header_values[0] !== 'Bearer')
        {
            $response = [
                'message' => 'failure',
                'body' => [
                    'error_msg' => 'Invalid Authorization header.'
                ]
            ];
            return json_encode($response);
        }

        $access_token = $auth_header_values[1];

        // Verify access token
        try 
        {
            $jwt = JWT::decode($access_token, $_ENV['APP_KEY'], ['HS256']);
        } 
        catch (Exception $ex)
        {
            $response = [
                'message' => 'failure',
                'body' => [
                    'error_msg' => 'Invalid access token: '.$ex->getMessage()
                ]
            ];

            return json_encode($response);
        }

        // Green light for user

        return json_encode($jwt);
    }
}