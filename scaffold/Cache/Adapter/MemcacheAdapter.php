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

class MemcacheAdapter extends CacheAdapter
{
    public $connection;

    public function setConnection($connection)
    {
        $this->connection=$connection;
    }

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        // TODO: Implement get() method.
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value)
    {
        // TODO: Implement set() method.
    }

    /**
     * @inheritDoc
     */
    public function multiGet(array $keys)
    {
        // TODO: Implement multiGet() method.
    }

    /**
     * @inheritDoc
     */
    public function multiSet(array $pairs)
    {
        // TODO: Implement multiSet() method.
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        // TODO: Implement has() method.
    }

    /**
     * @inheritDoc
     */
    public function expire($key, $time)
    {
        // TODO: Implement expire() method.
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        // TODO: Implement clear() method.
    }

    /**
     * @inheritDoc
     */
    public function delete($keys)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @inheritDoc
     */
    public function scan()
    {
        // TODO: Implement scan() method.
    }

}
