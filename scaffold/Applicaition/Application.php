<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午2:14
 */

namespace Scaffold\Application;

use Scaffold\Helper\Container;
use Scaffold\Http\Response;
use Scaffold\Http\ServerRequest;
use Scaffold\Log\Logger;
use Scaffold\Routing\Router;
use Scaffold\Session\Session;
use Scaffold\View\View;


/**
 *  Application
 * @property \Scaffold\Routing\Router $router
*  @property  \Scaffold\Http\ServerRequest $request
 * @property  \Scaffold\Http\Response $response
 * @property  \Scaffold\Log\Logger    $logger
 */
class Application
{
    protected $container;

    public function __construct()
    {
        $this->container=new Container();

        $this->container->singleton('request', function(){
            return new ServerRequest();
        });

        $this->container->singleton('response', function(){
           return new Response();
        });

        $this->container->singleton('logger', function(){
            return new Logger();
        });

        $this->container->singleton('router', function(){
            return new Router();
        });

        $this->container->singleton('session', function(){
            return new Session();
        });

        $this->container->singleton('view', function(){
            return new View();
        });
    }

    public function __get($name)
    {
        return $this->container->get($name);
    }

    public function __set($name, $value)
    {
        $this->container->set($name, $value);
    }

    public function __isset($name)
    {
        return $this->container->has($name);
    }

    public function __unset($name)
    {
        $this->container->remove($name);
    }

    /**
    *  include route file.
     * @param  string $filePath
    */
    public function sourceRouteFile($filePath)
    {
        require_once "$filePath";
    }

    /**
    *  dispatch request by router
    */
    public function dispatch()
    {
        $this->router->dispatch($this->request );
    }

    /**
    *  send header
    */
    public function sendHeader()
    {
        if( headers_sent() === false ){
            $headers=$this->response->getHeaders();
            foreach($headers as $name=>$values){
                foreach($values as $value){
                    header("$name: $value", false);
                }
            }
        }
    }

    /**
    *  send body
    */
    public function sendBody()
    {
        echo $this->response->getBody();
    }

    /**
    *  run , active all process.
    */
    public function  run()
    {
        try
        {
            $this->dispatch();
            $this->sendHeader();
            $this->sendBody();

            if (function_exists("fastcgi_finish_request")) {
                fastcgi_finish_request();
            }

        }
        catch(\Exception $e)
        {
            echo $e->getMessage() . '<br>';
            echo $e->getLine() . '<br>';
            print_r($e->getTrace());
            exit;
        }
    }
}
