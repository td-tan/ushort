<?php declare(strict_types = 1);

namespace Helper;

require('RequestData.php');

class Route
{

    // TODO Fix / mapping to controller not working
    public static function Get(string $route, string $controller) : bool
    {
        if($_SERVER['REQUEST_METHOD'] !== 'GET')
        {
            return false;
        }

        $url = parse_url($_SERVER['REQUEST_URI']);
        $path = $url['path'];

        $levels = substr_count($route, '/');

        if(substr_count($path, '/') !== $levels)
        {
            return false;
        }

        $route_parts = array_filter(explode('/', $route), 'trim');
        $url_parts = array_filter(explode('/', $path), 'trim');

        if (empty($route_parts) || empty($url_parts) || count($route_parts) !== count($url_parts))
        {
            return false;
        }

        foreach ($route_parts as $index => $part) {
            if(!preg_match("/$part/", $url_parts[$index]))
            {
                return false;
            }
        }

        // Get Controllers for mapping to action name
        $ctrl = explode('@', $controller);
        $ctrlName = $ctrl[0];
        $actionName = $ctrl[1];

        $ctrlObj = new $ctrlName;

        $namespace = explode('\\', $ctrlName);
        $ctrlName = end($namespace);


        require_once(__DIR__."/../Controller/$ctrlName.php");

        call_user_func_array([$ctrlObj, $actionName], [new RequestData($_GET, $_POST, $_SESSION, $_COOKIE)]);
        return true;
    }
}