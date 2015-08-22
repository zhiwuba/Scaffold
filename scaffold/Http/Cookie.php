<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-17
 * Time: 下午10:07
 */


namespace Scaffold\Http;

//FIXME
class Cookie
{
    protected $cookie;
    public function  __construct()
    {
        $this->cookie=&$_COOKIE;
    }

    public function get($name)
    {

    }

    public function set($name, $value, $expire, $path,$domain, $secure, $httponly )
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    public function delete($name)
    {
        unset($this->cookie[$name]);
    }

    public function clear()
    {
        foreach($this->cookie as $key=>$value)
        {
            unset($this->cookie[$key]);
        }
    }

}


