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
     * @inheritDoc
     */
    public function get($key)
    {
        $body=$this->connection->get($key);
        return $this->unserializeValue($body);
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value)
    {
        $body=$this->serializeValue($value);
        $ret=$this->connection->set($key, $body);
        return strval($ret)==='OK'? true : false;
    }

    /**
     * @inheritDoc
     */
    public function multiGet(array $keys)
    {
        $rawValues=$this->connection->mget($keys);
        $values=array_map(function($value){
            return $this->unserializeValue($value);
        }, $rawValues);
        return array_combine($keys, $values);
    }

    /**
     * @inheritDoc
     */
    public function multiSet(array $pairs)
    {
        array_walk($pairs, function(&$value, $key){
            $value=$this->serializeValue($value);
        });
        $ret=$this->connection->mset($pairs);
        return strval($ret)==='OK'? true : false;
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
        $cursor=null;
        while( $cursor!==0 )
        {
            $pos=$cursor===null? 0 : $cursor;
            list($cursor, $keys)=$this->connection->scan($pos);
            $pairs=$this->multiGet($keys);
            foreach($pairs as $key=>$value)
            {
                yield $key=>$value;
            }
        }
    }

}
