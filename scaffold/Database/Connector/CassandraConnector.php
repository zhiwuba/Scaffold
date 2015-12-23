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

class CassandraConnector extends Connector
{
	/**
	 * get default connection.
	 * @param $name string
	 * @return Object
	 */
	public function getConnection($name = '')
	{
		return NULL;
	}

	public static function loadConfig($config)
	{
		return new static();
	}
}