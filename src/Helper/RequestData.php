<?php declare(strict_types = 1);

namespace Helper;

class RequestData
{
    public array $get;
    public array $post;

    public function __construct(array $get = [], array $post = [])
    {
        $this->get = $get;
        $this->post = $post;
    }
}