<?php declare(strict_types = 1);

namespace App\Controller;

use Helper\RequestData;

class ApiController
{
    public function login(RequestData $rd)
    {
        sleep(3);
        echo json_encode(array("message" => "success"));
    }
}