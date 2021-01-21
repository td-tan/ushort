<?php declare(strict_types = 1);

namespace App\Helper;

class RequestData
{
    public array $query;
    public array $body;

    public function __construct(array $query = [], array $body = [])
    {
        $this->query = $query;
        $this->body = $body;
    }
}