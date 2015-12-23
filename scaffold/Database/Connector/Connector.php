<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scaffold\Database\Connector;

abstract class Connector
{

    /**
    *  load config file
     * @param $config array
     * @return Connector
    */
    public static function loadConfig($config)
    {
        $instance=new static($config);
        return $instance;
    }


	/**
	 * get default connection.
	 * @param $name string
	 * @return Object
	 */
	abstract public function getConnection($name='');

}
