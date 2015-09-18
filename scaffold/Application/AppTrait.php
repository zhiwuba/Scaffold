<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-9-18
 * Time: 下午2:31
 */

namespace Scaffold\Application;

trait AppTrait
{
    protected $app;

    public function getApplication()
    {
        return $this->app;
    }

    public function setApplication(Application $app)
    {
        $this->app=$app;
    }
}