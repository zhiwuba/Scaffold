<?php
/**
 * Created by PhpStorm.
 * User: explorer
 * Date: 2015/8/12
 * Time: 0:21
 */

namespace Scaffold\Helper;

class Container implements \ArrayAccess, \Countable, \IteratorAggregate
{
    protected $data=[];

    /**
    *  set instance, delay load instance.
     *
     * @param string $key
     * @param \Closure $closure
    */
    public function singleton($key, \Closure $closure )
    {
        if( !$this->has($key) )
        {
            $this->set($key,  $closure());
        }
    }

    /**
    *   interface implements.
    */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    public function count()
    {
        return count($this->data);
    }

    /**
    *  function get
    */
    public function get( $key, $default=null )
    {
        if( $this->has($key) ) {
            return $this->data[$key];
        }
        return $default;
    }

    public function set($key, $value)
    {
        $this->data[$key]=$value;
    }

    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    public function remove($key)
    {
        unset($this->data[$key]);
    }

}