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
    public $client;
    public function __construct($config)
    {
        $this->client=ClientBuilder::create()->setHosts($config)->build();
    }
}