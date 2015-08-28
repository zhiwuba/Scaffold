<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午2:14
 */

namespace Scaffold\Application;

use Scaffold\Helper\Container;

/**
 *  Application
 * @property \Scaffold\Routing\Router $router
*  @property  \Scaffold\Http\Request $request
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
            $uri=\Scaffold\Http\Uri::createFromEnv();
            return new \Scaffold\Http\Request($uri);
        });

        $this->container->singleton('response', function(){
           return new \Scaffold\Http\Response();
        });

        $this->container->singleton('logger', function(){
            return new \Scaffold\Log\Logger();
        });

        $this->container->singleton('router', function(){
            return new \Scaffold\Routing\Router();
        });

        $this->container->singleton('cookie', function(){
           return new \Scaffold\Http\Cookie();
        });

        $this->container->singleton('session', function(){
            return new \Scaffold\Session\Session();
        });

        $this->container->singleton('view', function(){
            return new \Scaffold\View\View();
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

    public function sourceRouteFile($filePath)
    {
        require_once "$filePath";
    }

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

    public function sendBody()
    {
        echo $this->response->getBody();
    }

    public function  run()
    {
        try
        {
            $this->router->dispatch($this->request->getUri(), $this->request->getMethod() );

            $this->sendHeader();
            $this->sendBody();
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
