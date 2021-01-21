<?php declare(strict_types = 1);

namespace App\Controller;

use Helper\RequestData;
use Firebase\JWT\JWT;
use App\Model\User;

class ApiController
{
    public function login(RequestData $rd)
    {

        $user = User::query()->where('email', '=', $rd->body['username']);
        if($user->count() < 1)
        {
            $response = [
                'message' => 'Failure',
                'body' => [
                    'error' => 'User does not exists.'
                ]
            ];
            return json_encode($response);
        }

        $user = $user->first();

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
            'message' => 'Success',
            'body' => [
                'access_token' => $jwt
            ]
        ];
        return json_encode($response);
    }
}