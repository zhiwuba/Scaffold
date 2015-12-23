<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test;

use Scaffold\Cache\Adapter\RedisAdapter;

class TestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        //$connector=new Connector();
        //\Scaffold\Database\Query\MysqlBuilder::setConnector($connector);
        //\Scaffold\Database\Query\CassandraBuilder::setConnection($connector);
        //\Scaffold\Database\Query\ElasticSearchBuilder::setConnection($connector);

        $redis=new \Predis\Client();
        \Scaffold\Cache\CacheItemPool::setAdapter(new RedisAdapter($redis));
        parent::setUp();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
}

