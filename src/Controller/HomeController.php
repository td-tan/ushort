<?php declare(strict_types = 1);

namespace App\Controller;

use Helper\RequestData;

class HomeController
{
    public function index(RequestData $rd)
    {
        return view('pages.home', []);
    }
}