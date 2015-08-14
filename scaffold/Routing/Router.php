<?php
/**
 * Created by PhpStorm.
 * User: explorer
 * Date: 2015/8/5
 * Time: 23:52
 */

namespace Scaffold\Routing;


class Router
{
    protected $routes;

    public function __construct()
    {

    }

    public static function get()
    {
        $args=func_get_args();
        self::addRoute($args);
    }

    public static function post()
    {
        $args=func_get_args();
        self::addRoute($args);
    }

    public static function put()
    {
        $args=func_get_args();
        self::addRoute($args);
    }

    public static function delete()
    {
        $args=func_get_args();
        return self::addRoute($args)->via();
    }

    protected function addRoute($args)
    {
        $pattern=array_shift($args);
        $callback=array_pop($args);
        $route=new Route($pattern, $callback);
        $this->routes[]=$route;
        return $route;
    }

    protected function findRoute($uri)
    {
        $routes=[];
        foreach($this->routes as $route)
        {
            if($route->matchPattern($uri) )
            {
                $routes[]=$route;
            }
        }
        return $routes;
    }



}

