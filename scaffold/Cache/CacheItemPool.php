<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-25
 * Time: 下午9:58
 */


namespace Scaffold\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Scaffold\Cache\Connection\CacheConnection;


class CacheItemPool implements CacheItemPoolInterface
{
    /**
     * @var CacheConnection
     */
    protected static $connection;

    public static function getConnection()
    {
        return static::$connection;
    }

    public static function setConnection($connection)
    {
        static::$connection=$connection;
    }

    /**
     * @inheritDoc
     */
    public function getItem($key)
    {
        $value=static::$connection->get($key);
        $item=new CacheItem($key, $value);
        return $item;
    }

    /**
     * @inheritDoc
     */
    public function getItems(array $keys = array())
    {
        static::$connection->get();
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key)
    {
        static::$connection->has();
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        static::$connection->clear();
    }

    /**
     * @inheritDoc
     */
    public function deleteItem($key)
    {

    }

    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys)
    {

    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item)
    {

    }

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item)
    {

    }

    /**
     * @inheritDoc
     */
    public function commit()
    {

    }

}


