<?php declare(strict_types = 1);

namespace App\Controller;

use App\Helper\RequestData;
use App\Model\Link;
use App\Helper\Utils;

class HomeController
{
    public function index(RequestData $rd) : string
    {
        return view('pages.home', []);
    }

    public function short(RequestData $rd) : string
    {
        if(isset($rd->query['id']))
        {
            return json_encode(Utils::error_message('Short does not exist.'));
        }

        $link = Link::query()->where('short', '=', $rd->query['id'])->first();
        if(empty($link) || $link->deleted)
        {
            return '';
        }

        $validated_url = filter_var($link->link, FILTER_VALIDATE_URL);
        $clean_url = filter_var($validated_url, FILTER_SANITIZE_URL);
        if ($_ENV['DEBUG'])
        {
            return json_encode($rd->query);
        }
        
        header("Location: $clean_url"); // TODO Validate link
        return '';
    }
}