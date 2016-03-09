<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class Queue
{

    static $adapter;

    public static function getAdapter()
    {
        return static::$adapter;
    }

    public static function setAdapter($adapter)
    {
        static::$adapter=$adapter;
    }


    public function push()
    {

    }

	public function pop()
	{

	}
}

