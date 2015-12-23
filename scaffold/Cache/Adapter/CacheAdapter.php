<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午3:12
 */

namespace Scaffold\Cache\Adapter;

abstract class CacheAdapter
{
    public static $defaultTTL='86400'; //one day
    /**
     * @var
     */
    protected $connection;

    public function __construct($connection)
    {
        $this->connection=$connection;
    }

    /**
     *  get key from cache
     *
     * @param $key string
     * @return integer|string|array|object
     */
    abstract public function get($key);

    /**
     *  set the value of key,
     *
     * @param $key
     * @param $value
     * @return  bool
     */
    abstract public function set($key, $value);

    /**
     *  get value of keys from cache
     *
     * @param array $keys
     * @return mixed
     */
    abstract public function multiGet(array $keys);


    /**
     *  set the value of keys
     *
     * @param array $pairs
     * @return mixed
     */
    abstract public function multiSet(array $pairs);

    /**
     *  has the key.
     *
     * @param $key
     * @return bool
     */
    abstract public function has($key);


    /**
     *  expire the key
     *
     * @param $key string
     * @param $time null|'none'|int
     * @return mixed
     */
    abstract public function expire($key, $time);


    /**
     *  clear the cache
     *
     * @return bool
     */
    abstract public function clear();


    /**
     *  delete the keys
     *
     * @param $keys array
     * @return bool
     */
    abstract public function delete($keys);


    /**
     *  scan the cache
     *
     * @return [$key=>$value]
     */
    abstract public function scan();


    /**
     *  serialize value
     *
     * @param $value
     * @return string
     */
    public function serializeValue($value)
    {
        //todo
    }

    /**
     * unserialize value
     *
     * @param $content
     * @return mixed
     */
    public function unserializeValue($content)
    {
        //todo
    }

}



