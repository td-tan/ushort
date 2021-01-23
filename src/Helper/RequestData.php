<?php declare(strict_types = 1);

namespace App\Helper;

class RequestData
{
    public array $query;
    public array $body;
    public array $cookies;

    public function __construct(array $query = [], array $body = [], array $cookies = [])
    {
        $this->query = $query;
        $this->body = $body;
        $this->cookies = $cookies;
    }
}