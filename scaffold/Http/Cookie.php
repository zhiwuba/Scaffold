<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-17
 * Time: 下午10:07
 */


namespace Scaffold\Http;

class Cookie
{
    protected $cookie;
    public function  __construct()
    {
        $this->cookie=$_COOKIE;
    }

    public function get()
    {

    }

    public function set()
    {

    }

    public function delete()
    {

    }

    public function clear()
    {

    }
}


