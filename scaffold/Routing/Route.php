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
    protected $regex;

    protected $method=[];
    protected $params=[];


    public function __construct($pattern, $callback)
    {
        $this->setPattern($pattern);
        $this->setCallback($callback);
    }

    /**
    *  http method
     */
    public function via()
    {
        $args=func_get_args();
        $this->method=array_merge($this->method, $args);
    }

    public function supportMethod($method)
    {
        $isSupport=in_array($method, $this->method);
        return $isSupport;
    }

    /**
    *   get params from uri.
    */
    public function getParams()
    {
        return $this->params;
    }

    public function setParams(array $params)
    {
        $this->params=array_merge($params, $this->params);
    }

    /**
    *  get or set callback
    */

    public function getCallback()
    {
        return $this->callback;
    }

    public function setCallback($callback)
    {
        if( is_string($callback) || is_callable($callback) )
        {
            $this->callback=$callback;
        }
        else
        {
            throw new \InvalidArgumentException("invalid callback.");
        }
    }

    /**
    *  get pattern of route.
    */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
    *  set pattern and regex of route.
    */
    public function setPattern($pattern)
    {
        $parts=[];
        $segments=explode('/', $pattern);
        foreach($segments as $segment)
        {
            if( strpos($segment, ':')===0 )
            {
                $name=substr($segment,1);
                $parts[]="(?P<$name>\\w+)";
            }
            else
            {
                $parts[]="$segment";
            }
        }

        $this->pattern=$pattern;
        $this->regex='#' . implode('/', $parts)  .  '#';
    }

    public function matchPattern($uri)
    {
        $isMatch=preg_match($this->regex, $uri, $matches);
        if( $isMatch)
        {
            foreach($matches as $key=>$value )
            {
                if( !empty($key) && is_string($key) )
                {
                    $this->params[$key]=$value;
                }
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    public function dispatch()
    {
        if( is_string($this->callback) )
        {
            $parts=explode('@' , $this->callback);
            if( count($parts)==2 )
            {
                list($controllerName, $function)=$parts;
                $controller=new $controllerName;
                call_user_func_array([$controller, $function], $this->params);
            }
            else
            {
                throw new \Exception(__CLASS__ . __FUNCTION__ . "");
            }
        }
        else if(is_array($this->callback))
        {
            //TODO
        }
        else if( is_callable($this->callback) )
        {   //function or closure
            call_user_func_array($this->callback, $this->params);
        }
        else
        {
            throw new \Exception(__CLASS__ . __FUNCTION__ . "route can't dispatch.");
        }
    }
}