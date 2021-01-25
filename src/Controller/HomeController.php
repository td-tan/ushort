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
        $link = Link::query()->where('short', '=', $rd->query['id'])->first();
        if(empty($link))
        {
            return '';
        }

        $validated_url = filter_var($link->link, FILTER_VALIDATE_URL);
        $clean_url = filter_var($validated_url, FILTER_SANITIZE_URL);
        header("Location: $clean_url"); // TODO Validate link
        return '';
    }
}