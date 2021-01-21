<?php declare(strict_types = 1);

namespace App\Helper;

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
}