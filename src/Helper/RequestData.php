<?php declare(strict_types = 1);

namespace App\Helper;

class RequestData
{
    public array $query;
    public array $body;
    public array $cookie;

    public function __construct(array $query = [], array $body = [], array $cookie = [])
    {
        $this->query = $query;
        $this->body = $body;
        $this->cookie = $cookie;
    }
}