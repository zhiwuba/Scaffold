<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午2:14
 */


namespace Scaffold\Application;


class Application
{
    protected $container;

    public function __construct()
    {
        $this->container=new \Slim\Helper\Container();
    }

    public function  run()
    {

    }
}