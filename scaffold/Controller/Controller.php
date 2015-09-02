<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午7:56
 */


namespace Scaffold\Controller;

use Scaffold\Application\Application;

abstract class Controller
{
    /**
    *  @var \Scaffold\Application\Application;
    */
    protected $app;

    /**
    * @return Application
    */
    public function getApplication()
    {
        return $this->app;
    }

    /**
    * @param $app Application
    */
    public function setApplication(Application $app)
    {
        $this->app=$app;
    }


    public function _before()
    {

    }

    public function _after()
    {

    }
}

