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

    protected $method;
    protected $params;


    public function __construct($pattern, $callback)
    {
        $this->pattern=$pattern;
        $this->callback=$callback;
    }

    /**
    *   get params from uri.
    */
    public function getParams()
    {
        return $this->params;
    }

    public function setParams($params)
    {
        $this->params=array_merge($params, $this->params);
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
                $parts[]="(<?name=$name>\\w+)";
            }
            else
            {
                $parts[]="$segment";
            }
        }

        $this->pattern=$pattern;
        $this->regex='/^' . implode('/', $parts)  .  '$/';;
    }

    public function matchPattern($uri)
    {
        $count=preg_match($this->regex, $uri, $matches);
        if( $count==1 ) //匹配
        {
            $this->params=$matches;
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
                $controllerName=$parts[0];
                $function = $parts[1];
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