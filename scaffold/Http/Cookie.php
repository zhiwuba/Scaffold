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

    public function get($name, $default=NULL)
    {
        if( isset($this->cookie[$name]) )
        {
            return $this->cookie[$name];
        }
        else
        {
            return $default;
        }
    }

    public function set($name, $value, $expire, $path,$domain, $secure, $httpOnly )
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
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


