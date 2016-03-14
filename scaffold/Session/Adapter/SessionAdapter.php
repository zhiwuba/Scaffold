<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scaffold\Session\Adapter;

abstract class SessionAdapter
{
    /**
     * @param $session_id string
     * @return bool
     */
    abstract public function exists($session_id);

    abstract public function get($session_id);

    abstract public function set($session_id, $data);

    abstract public function expire($maxLifeTime);

    abstract public function del($session_id);

}
