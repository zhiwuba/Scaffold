<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-25
 * Time: 下午9:58
 */


namespace Scaffold\Cache;

/**
 * \Scaffold\Cache\CacheItemPool generates Cache\CacheItem objects.
 */
class CacheItemPool
{

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return an ItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key
     *   The key for which to return the corresponding Cache Item.
     * @return \Scaffold\Cache\CacheItem
     *   The corresponding Cache Item.
     * @throws \InvalidArgumentException
     *   If the $key string is not a legal value a \InvalidArgumentException
     *   MUST be thrown.
     */
    public function getItem($key)
    {

    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param array $keys
     * An indexed array of keys of items to retrieve.
     * @return array|\Traversable
     * A traversable collection of Cache Items keyed by the cache keys of
     * each item. A Cache item will be returned for each key, even if that
     * key is not found. However, if no keys are specified then an empty
     * traversable MUST be returned instead.
     */
    public function getItems(array $keys = array())
    {

    }

    /**
     * Deletes all items in the pool.
     *
     * @return boolean
     *   True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {

    }

    /**
     * Removes multiple items from the pool.
     *
     * @param array $keys
     * An array of keys that should be removed from the pool.
     * @return static
     * The invoked object.
     */
    public function deleteItems(array $keys)
    {

    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItem $item
     *   The cache item to save.
     *
     * @return static
     *   The invoked object.
     */
    public function save(CacheItem $item)
    {

    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItem $item
     *   The cache item to save.
     * @return static
     *   The invoked object.
     */
    public function saveDeferred(CacheItem $item)
    {

    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     *   TRUE if all not-yet-saved items were successfully saved. FALSE otherwise.
     */
    public function commit()
    {

    }

}