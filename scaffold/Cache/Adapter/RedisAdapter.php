<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scaffold\Cache\Adapter;

class RedisAdapter extends CacheAdapter
{
    /**
     * @var  \Predis\Client
     */
    protected $connection;

    /**
     * @var int
     */
    protected $cursor=null;


    /**
     * @inheritDoc
     */
    public function get($key)
    {
        $value=$this->connection->get($key);
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value)
    {
        $ret=$this->connection->set($key, $value);
        return $ret; //todo
    }

    /**
     * @inheritDoc
     */
    public function multiGet(array $keys)
    {
        $values=$this->connection->mget($keys);
        return $values; //todo
    }

    /**
     * @inheritDoc
     */
    public function multiSet(array $pairs)
    {
        $ret=$this->connection->mset($pairs);
        return $ret; //todo
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        return $this->connection->exists($key);
    }

    /**
     * @inheritDoc
     */
    public function expire($key, $time)
    {
        if( $time==='none' )
        {   //persist
            $this->connection->persist($key);
        }
        else if($time===null)
        {
            $this->connection->expire($key, static::$defaultTTL);
        }
        else
        {
            $this->connection->expire($key, $time);
        }
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->connection->flushall();

    }

    /**
     * @inheritDoc
     */
    public function delete($keys)
    {
        return $this->connection->del($keys);
    }

    /**
     * @inheritDoc
     */
    public function scan()
    {
        if( $this->cursor===0 )
        {
            $this->cursor=null;
            return [];
        }

        $pos=$this->cursor===null? 0 : $this->cursor;
        $ret=$this->connection->scan($pos);
        $this->cursor=$ret[0];
        return $ret[1];
    }

}
