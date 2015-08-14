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
            return new \Scaffold\Http\Request();
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


    public function  run()
    {
        $this->router->call();

        if( headers_sent() === false ){
            $headers=$this->response->getHeaders();
            foreach($headers as $name=>$values){
                foreach($values as $value){
                    header("$name: $value", false);
                }
            }
        }

        echo $this->response->getBody();
    }
}
