<?php declare(strict_types = 1);

namespace App\Controller;

use Firebase\JWT\JWT;
use App\Model\User;
use App\Helper\Utils;
use App\Helper\RequestData;
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
            return json_encode(Utils::error_message('User does not exists.'));
        }

        $user = $user->first();

        // Guard: Verify password hash
        if (!password_verify($rd->body['password'], $user->password))
        {
            return json_encode(Utils::error_message('Password is wrong.'));
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
            return json_encode(Utils::error_message('No Authorization header.'));
        }
        $auth_header_values = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);

        if($auth_header_values < 2)
        {
            return json_encode(Utils::error_message('No access token.'));
        }

        if($auth_header_values[0] !== 'Bearer')
        {
            return json_encode(Utils::error_message('Invalid Authorization header.'));
        }

        $access_token = $auth_header_values[1];

        // Verify access token
        try 
        {
            $jwt = JWT::decode($access_token, $_ENV['APP_KEY'], ['HS256']);
        } 
        catch (Exception $ex)
        {
            return json_encode(Utils::error_message('Invalid access token: '.$ex->getMessage()));
        }

        // Green light for user
        
        return json_encode($jwt);
    }
}