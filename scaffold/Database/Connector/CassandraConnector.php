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
use Cassandra;
use Cassandra\Session;

class CassandraConnector extends Connector
{
    protected $configs;

    protected $connection;

    public function __construct($configs)
    {
        $this->configs=$configs;
    }

    /**
	 * get default connection.
	 * @param $name string
	 * @return Session
	 */
	public function getConnection($name = '')
                                                                                                                                                                                        	{
        if( !isset($this->connection) )
        {
            if( is_string($this->configs['hosts']) )
                $points=$this->configs['hosts'];
            else
                $points=implode(',', $this->configs['hosts']);

            $port=$this->configs['port'];
            $keySpace=$this->configs['keyspace'];

            $cluster=Cassandra::cluster()->withContactPoints($points)->withPort($port)->build();
            $this->connection=$cluster->connect($keySpace);
        }
		return $this->connection;
	}

	public static function loadConfig($configs)
	{
		return new static($configs);
	}
}