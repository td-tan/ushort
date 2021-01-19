<?php declare(strict_types = 1);

namespace Helper;

require('RequestData.php');

// TODO Implement ErrorController
// TODO Better route matching: \{(\w+)=(alpha)?\|?(digit)?\}
class Route
{
    public static function match(string $route, string $url)
    {
        $url = parse_url($_SERVER['REQUEST_URI']);
        $path = $url['path'];

        $param_rule = "/\{(?'param'\w+)(?'rules'=\w+?\|?\w+)?\}/";

        // Convert parameter rule into pattern
        if(preg_match($param_rule, $route, $matches, PREG_OFFSET_CAPTURE))
        {
            $param = $matches['param'][0];
            $offset = $matches['param'][1]-1;
            $route = substr_replace($route, "(?'$param'[a-zA-Z0-9]+)", $offset);
        }

        // Convert route into pattern
        $pattern = str_replace('/', '\/', $route);
        if (!preg_match("/^$pattern$/", $path, $matches))
        {
            return False;
        }
        return $matches;
    }

    public static function loadController(string $controller, RequestData $rd) : void
    {
        // Get Controllers for mapping to action name
        $ctrl = explode('@', $controller);
        $ctrlName = $ctrl[0];
        $actionName = $ctrl[1];

        $ctrlObj = new $ctrlName;

        // Remove namespace from Controller Name
        $namespace = explode('\\', $ctrlName);
        $ctrlName = end($namespace);


        require_once(__DIR__."/../Controller/$ctrlName.php");

        call_user_func_array([$ctrlObj, $actionName], [$rd]);
    }

    // TODO Fix / mapping to controller not working
    public static function Get(string $route, string $controller) : bool
    {
        if($_SERVER['REQUEST_METHOD'] !== 'GET')
        {
            return False;
        }

        $query = self::match($route, $_SERVER['REQUEST_URI']);
        if($query === False)
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

        self::loadController($controller, new RequestData($query));

        
        return true;
    }
}