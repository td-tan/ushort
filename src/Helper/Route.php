<?php declare(strict_types = 1);

namespace Helper;

require('RequestData.php');

class Route
{
    public static function match(string $route, string $url) : bool
    {
        $url = parse_url($_SERVER['REQUEST_URI']);
        $path = $url['path'];

        // Convert route into pattern
        $pattern = str_replace('/', '\/', $route);
        var_dump($pattern);
        if (!preg_match("/^$pattern$/", $path))
        {
            return False;
        }
        var_dump($path);
        return True;
    }

    public static function loadController(string $controller)
    {
        // Get Controllers for mapping to action name
        $ctrl = explode('@', $controller);
        $ctrlName = $ctrl[0];
        $actionName = $ctrl[1];

        $ctrlObj = new $ctrlName;

        $namespace = explode('\\', $ctrlName);
        $ctrlName = end($namespace);


        require_once(__DIR__."/../Controller/$ctrlName.php");

        call_user_func_array([$ctrlObj, $actionName], [new RequestData($_GET, $_POST, $_SESSION, $_COOKIE)]);
    }

    // TODO Fix / mapping to controller not working
    public static function Get(string $route, string $controller) : bool
    {
        if($_SERVER['REQUEST_METHOD'] !== 'GET')
        {
            return False;
        }

        if (!self::match($route, $_SERVER['REQUEST_URI']))
        {
            return False;
        }

        /*
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
        */

        self::loadController($controller);

        
        return true;
    }
}