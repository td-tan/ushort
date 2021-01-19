<?php declare(strict_types = 1);

namespace Helper;

require('RequestData.php');

// TODO Implement ErrorController
// TODO Better route matching: \{(\w+)=(alpha)?\|?(digit)?\}
class Route
{

    static string $route = "";

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
        return array($param => $matches[$param]); // We only want Get parameter
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
    
    public static function Get(string $route, string $controller) : bool
    {
        if($_SERVER['REQUEST_METHOD'] !== 'GET')
        {
            return False;
        }

        $query = self::match(self::$route.$route, $_SERVER['REQUEST_URI']);
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

    public static function Post(string $route, string $controller)
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST')
        {
            return False;
        }

        $query = self::match(self::$route.$route, $_SERVER['REQUEST_URI']);
        if($query === False || $query === null)
        {
            return False;
        }

        self::loadController($controller, new RequestData($query, $_POST));

        
        return true;
    }

    // TODO Implement all HTTP METHOD

    public static function Group(string $top_route, $callback)
    {
        self::$route = $top_route;
        call_user_func($callback);
        // Reset out of scope
        self::$route = "";
    }
}