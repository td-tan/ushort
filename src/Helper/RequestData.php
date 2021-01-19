<?php declare(strict_types = 1);

namespace Helper;

class RequestData
{
    public array $query;
    public array $data;

    public function __construct(array $query = [], array $data = [])
    {
        $this->query = $query;
        $this->data = $data;
    }
}