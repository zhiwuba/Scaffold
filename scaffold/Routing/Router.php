<?php
/**
 * Created by PhpStorm.
 * User: explorer
 * Date: 2015/8/5
 * Time: 23:52
 */

namespace Scaffold\Routing;
use Scaffold\Http\Request;
use Scaffold\Http\ServerRequest;
use Scaffold\Application\AppTrait;

class Router
{
    use AppTrait;

    /**
    *  @var \Scaffold\Routing\Route[]
    */
    protected static $routes;

    /**
    *  construct.
    */
    public function __construct($app)
    {
        $this->setApplication($app);
    }

    /**
    *  get
    */
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

    protected static function addRoute($args)
    {
        $pattern=array_shift($args);
        $callback=array_pop($args);
        $route=new Route($pattern, $callback);
        self::$routes[]=$route;
        return $route;
    }

    /**
    * @return \Scaffold\Routing\Route[]
     */
    public function findRoute($uri, $method)
    {
        $routes=[];
        foreach(self::$routes as $route)
        {
            if($route->matchPattern($uri) && $route->supportMethod($method))
            {
                $routes[]=$route;
            }
        }
        return $routes;
    }

    /**
    *  dispatch request by router.
     * @param $request ServerRequest
    */
    public function dispatch(ServerRequest &$request)
    {
        $uri = $request->getUri();
        $method = $request->getMethod();
        $routes=$this->findRoute($uri->getPath(), $method );
        foreach($routes as $route)
        {
            $route->dispatch();
        }
    }

}

