<?php declare(strict_types = 1);

namespace App\Controller;

use Helper\RequestData;
use Firebase\JWT\JWT;
use App\Model\User;

class ApiController
{
    public function login(RequestData $rd)
    {

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
            "exp" => time() + 60
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
}