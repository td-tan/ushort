<?php declare(strict_types = 1);

namespace App\Controller;

use Firebase\JWT\JWT;
use App\Model\User;
use App\Helper\Utils;
use App\Helper\RequestData;
use App\Model\Token;
use Exception;

class ApiController
{
    public function login(RequestData $rd) : string
    {
        header('Content-Type: application/json');
        // TODO Generalize error message, should not be verbose
        if(!isset($rd->body['username'], $rd->body['password']))
        {
            return json_encode(Utils::error_message('No username or password.'));
        }

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

        // TODO Refactor jwt token & refresh_token generate logic

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
        $client_ip_addr = $_SERVER['REMOTE_ADDR'];

        $bytes = random_bytes(32);

        $jwt_refresh_token = bin2hex(uniqid('', True).$bytes.$client_ip_addr);

        // Store refresh_token in db
        // Prepare token
        $token = new Token();
        $token->ip_addr = $client_ip_addr;
        $token->refresh_token = $jwt_refresh_token;

        // Expire window
        $expire_date_time = new \DateTime();
        $expire_date_time->modify('+1 day');

        $token->expire_at = $expire_date_time->format('Y-m-d H:i:s'); // 1 day refresh token lifetime

        // User has only one token
        $user_token = $user->token();
        if ($user_token->count() < 1)
        {
            $user_token->save($token);
        }
        else
        {
            $user_token = $user_token->first();
            $user_token->ip_addr = $token->ip_addr;
            $user_token->refresh_token = $token->refresh_token;
            $user_token->expire_at = $token->expire_at;
            $user_token->save();
        }

        setcookie('refresh_token', $token->refresh_token, [
            'expires' => strtotime($token->expire_at), 
            'path' => '/api/refresh', 
            'domain' => '', 
            'secure' => False, 
            'httponly' => True, 
            'samesite' => 'Lax',
        ]);


        $response = [
            'message' => 'success',
            'body' => [
                'access_token' => $jwt,
                'refresh_token' => $jwt_refresh_token
            ]
        ];
        return json_encode($response);
    }

    public function get_user(RequestData $rd) : string
    {
        // TODO Refactor jwt verification logic
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

    public function refresh_token(RequestData $rd) : string
    {
        // TODO Refactor jwt verification logic
        header('Content-Type: application/json');

        return json_encode($_COOKIE);

        if(!isset($rd->body['refresh_token']))
        {
            return json_encode(Utils::error_message('No refresh token.'));
        }


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

        // TODO Refactor jwt token & refresh_token generate logic
        // Get user by jwt sub id
        $user = User::query()->find((int)$jwt->sub);
        // TODO Invalidate old jwt access_token, check refresh_token
        // TODO Check if refresh expired
        // TODO Invalidate refresh_token on logout

        if($rd->body['refresh_token'] !== $user->token->refresh_token && date("Y-m-d H:i:s", strtotime($user->token->expire_at)) < date("Y-m-d H:i:s"))
        {
            return json_encode(Utils::error_message('Invalid refresh token.'));
        }

        // TODO Issue new token
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
        
        $client_ip_addr = $_SERVER['REMOTE_ADDR'];

        $bytes = random_bytes(32);

        $jwt_refresh_token = bin2hex(uniqid('', True).$bytes.$client_ip_addr);

        // Store refresh_token in db
        // Prepare token
        $token = new Token();
        $token->ip_addr = $client_ip_addr;
        //$token->refresh_token = $jwt_refresh_token;

        // Expire window
        $datetime = new \DateTime('NOW');
        $datetime->modify('+1 day');

        $token->expire_at = $datetime->format('Y-m-d H:i:s'); // 1 day refresh token lifetime

        // User has only one token
        $user_token = $user->token();
        /*if ($user_token->count() < 1)
        {
            $user_token->save($token);
        }*/
        if($user->count() > 1)
        {
            $user_token = $user_token->first();
            $user_token->ip_addr = $token->ip_addr;
            //$user_token->refresh_token = $token->refresh_token;
            $user_token->expire_at = $token->expire_at;
            $user_token->save();
        }


        $response = [
            'message' => 'success',
            'body' => [
                'access_token' => $jwt,
                'refresh_token' => $jwt_refresh_token
            ]
        ];
        return json_encode($response);
    }
}