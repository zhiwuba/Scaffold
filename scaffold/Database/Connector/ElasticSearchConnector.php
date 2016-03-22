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

use Elasticsearch\ClientBuilder;

class ElasticSearchConnector extends Connector
{
	protected $configs;

	/**
	* @var \Elasticsearch\Client
	*/
    protected $connection;

    public function __construct($configs)
    {
        $this->configs=$configs;
        $this->client=ClientBuilder::create()->setHosts($configs['hosts'])->build();
    }

    /**
     * @inheritDoc
     */
    public static function loadConfig($config)
    {
        return new static($config);
    }

    /**
	 * get default connection.
	 * @param $name string
	 * @return \Elasticsearch\Client
	 */
	public function getConnection($name='')
	{
        if( !$this->connection )
        {
            return $this->client;
        }
        return $this->connection;
	}

}