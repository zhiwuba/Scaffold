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
use Scaffold\Middleware\Middleware;

abstract class Controller
{
    use AppTrait;

    /**
     * @var Middleware[]
     */
    protected $middleWares;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        foreach( $this->middleWares as $middleWare )
        {
            $middleWare->call();
        }
    }

    public function registerMiddleware()
    {
        $this->middleWares;
    }

}

