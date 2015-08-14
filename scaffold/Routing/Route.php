<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午7:57
 */

namespace Scaffold\Routing;

class Route
{
    protected $pattern;
    protected $callback;

    public function __construct()
    {

    }

    public function matchPattern()
    {
        return true;
    }

}