<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scaffold\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Scaffold\Cache\Adapter\CacheAdapter;


class CacheItemPool implements CacheItemPoolInterface
{
    /**
     * @var CacheItem[]
     */
    protected $deferItems;

    /**
     * @var CacheAdapter
     */
    protected static $adapter;

    public static function getAdapter()
    {
        return static::$adapter;
    }

    public static function setAdapter($adapter)
    {
        static::$adapter=$adapter;
    }

    /**
     * @inheritDoc
     */
    public function getItem($key)
    {
        if( $this->isValidKey($key) )
        {
            if( static::$adapter->has($key) )
            {
                $value=static::$adapter->get($key);
                $item=new CacheItem($key, $value, true);
            }
            else
            {
                $item=new CacheItem($key, null);
            }
            return $item;
        }
        else
        {
            throw new InvalidArgumentException("$key is not  a legal value.");
        }
    }

    /**
     * @inheritDoc
     */
    public function getItems(array $keys = array())
    {
        if( empty($keys) )
        {   // null keys
            foreach(static::$adapter->scan() as $key=> $value)
            {
                $item=new CacheItem($key, $value, true);
                yield $key=>$item;
            }
        }
        else
        {
            foreach($keys as $key)
            {
                if($this->isValidKey($key))
                {
                    if( static::$adapter->has($key) )
                    {
                        $value=static::$adapter->get($key);
                        $item=new CacheItem($key, $value, true);
                    }
                    else
                    {
                        $item=new CacheItem( $key, null, false);
                    }
                    yield $key=>$item;
                }
                else
                {
                    throw new InvalidArgumentException("$key is not a legal value.");
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key)
    {
        if( $this->isValidKey($key) )
        {
            return static::$adapter->has($key);
        }
        else
        {
            throw new InvalidArgumentException("$key is not a legal value.");
        }
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        return static::$adapter->clear();
    }

    /**
     * @inheritDoc
     */
    public function deleteItem($key)
    {
        if( $this->isValidKey($key) )
        {
            return static::$adapter->delete([$key]);
        }
        else
        {
            throw new InvalidArgumentException("$key is not a legal value.");
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys)
    {
        foreach($keys as $key)
        {
            if( !$this->isValidKey($key) )
            {
                throw new InvalidArgumentException("$key is not a legal value.");
            }
        }

        return static::$adapter->delete($keys);
    }


    /**
     * @param CacheItem $item
     * @return bool
     */
    public function save(CacheItemInterface $item)
    {
        $key=$item->getKey();
        $value=$item->getValue();
        return static::$adapter->set($key, $value);
    }

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferItems[]=$item;
    }

    /**
     * @inheritDoc
     */
    public function commit()
    {
        foreach($this->deferItems as $item)
        {
            if( static::$adapter->set($item->getKey(), $item->getValue()) )
            {
                return false;
            }
        }
        return true;
    }

    /**
     * valid key
     *
     * @param $key
     * @return bool
     */
    protected function isValidKey($key)
    {
        if( is_string($key) && preg_match("#[a-zA-Z0-9_\\.]+#", $key) )
            return true;
        else
            return false;
    }

}


