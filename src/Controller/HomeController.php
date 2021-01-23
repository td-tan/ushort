<?php declare(strict_types = 1);

namespace App\Controller;

use App\Helper\RequestData;
use App\Model\Link;

class HomeController
{
    public function index(RequestData $rd) : string
    {
        return view('pages.home', []);
    }

    public function short(RequestData $rd) : string
    {
        $links = Link::query()->where('short', '=', $rd->query['id']);
        if(empty($links))
        {
            return '';
        }

        $link = $links->first()->link;
        return header("Location: $link");
    }
}