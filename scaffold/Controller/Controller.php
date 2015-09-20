<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午7:56
 */


namespace Scaffold\Controller;

use Scaffold\Application\Application;
use Scaffold\Application\AppTrait;

abstract class Controller
{
    use AppTrait;

    public function beforeFilter()
    {

    }

    public function afterFilter()
    {

    }
}

