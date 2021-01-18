<?php declare(strict_types = 1);

namespace Helper;

class RequestData
{
    public array $get;
    public array $post;
    public array $session;
    public array $cookie;

    public function __construct(array $get, array $post, array $session, array $cookie)
    {
        $this->get = $get;
        $this->post = $post;
        $this->session = $session;
        $this->cookie = $cookie;
    }
}