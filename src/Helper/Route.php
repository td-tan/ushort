<?php declare(strict_types = 1);

namespace App\Helper;


//require('RequestData.php');

// TODO Implement ErrorController
// TODO Better route matching: \{(\w+)=(alpha)?\|?(digit)?\}
class Route
{

    static string $route = "";

    public static function match(string $route, string $url) : ?array
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
            return Null;
        }

        if(count($matches) < 2)
        {
            return [];
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

        print call_user_func_array([$ctrlObj, $actionName], [$rd]);
    }

    public static function mapping(string $verb, string $route)
    {
        if($_SERVER['REQUEST_METHOD'] !== $verb)
        {
            return False;
        }

        $query = self::match(self::$route.$route, $_SERVER['REQUEST_URI']);
        if(is_null($query))
        {
            return False;
        }
        return $query;
    }    

    public static function Get(string $route, string $controller) : bool
    {
        $query = self::mapping('GET', $route);

        if($query === False) 
        {
            return False;
        }
        self::loadController($controller, new RequestData($query));

        
        return True;
    }

    public static function Post(string $route, string $controller)
    {
        $query = self::mapping('POST', $route);

        if($query === False) 
        {
            return False;
        }

        $json_data = json_decode(file_get_contents('php://input'), true);

        if($json_data)
        {
            $_POST = $json_data;
        }

        self::loadController($controller, new RequestData($query, $_POST));

        
        return true;
    }

    public static function Put(string $route, string $controller)
    {
        $query = self::mapping('PUT', $route);

        if($query === False) 
        {
            return False;
        }
        $json_data = json_decode(file_get_contents('php://input'), true);

        if($json_data)
        {
            $_POST = $json_data;
        }

        self::loadController($controller, new RequestData($query, $_POST));

        
        return true;
    }

    public static function Delete(string $route, string $controller)
    {
        $query = self::mapping('DELETE', $route);

        if($query === False) 
        {
            return False;
        }

        self::loadController($controller, new RequestData($query));

        
        return true;
    }

    // TODO Implement PATCH HTTP METHOD

    public static function Group(string $top_route, $callback)
    {
        self::$route = $top_route;
        call_user_func($callback);
        // Reset out of scope
        self::$route = "";
    }
}