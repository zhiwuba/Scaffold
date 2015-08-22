<?php
/**
 * Created by PhpStorm.
 * User: explorer
 * Date: 2015/8/5
 * Time: 23:52
 */

namespace Scaffold\Routing;
use Scaffold\Http\Request;

class Router
{
    protected $routes;

    public function __construct()
    {

    }

    public static function get()
    {
        $args=func_get_args();
        self::addRoute($args)->via(Request::METHOD_GET);
    }

    public static function post()
    {
        $args=func_get_args();
        self::addRoute($args)->via(Request::METHOD_POST);
    }

    public static function put()
    {
        $args=func_get_args();
        self::addRoute($args)->via(Request::METHOD_PUT);
    }

    public static function delete()
    {
        $args=func_get_args();
        self::addRoute($args)->via(Request::METHOD_DELETE);
    }

    public static function any()
    {
        $args=func_get_args();
        self::addRoute($args)->via("ANY");
    }

    protected function addRoute($args)
    {
        $pattern=array_shift($args);
        $callback=array_pop($args);
        $route=new Route($pattern, $callback);
        $this->routes[]=$route;
        return $route;
    }

    /**
    * @return array Scaffold\Routing\Route
     */
    public function findRoute($uri, $method)
    {
        $routes=[];
        foreach($this->routes as $route)
        {
            if($route->matchPattern($uri) && $route->supportMethod($method))
            {
                $routes[]=$route;
            }
        }
        return $routes;
    }

    public function dispatch($uri, $method)
    {
        $routes=$this->findRoute($uri, $method );
        foreach($routes as $route)
        {
            $route->dispatch();
        }
    }


}

