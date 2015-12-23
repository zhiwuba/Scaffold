<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Cache;

use Scaffold\Cache\Adapter\RedisAdapter;
use Scaffold\Cache\CacheItemPool;
use \Test\TestCase;

class RedisAdapterTest extends TestCase
{
    /**
     * @skip
     */
    public function testBaseOperate()
    {
        /**
         * @var RedisAdapter
         */
        $client=CacheItemPool::getAdapter();

        $this->assertTrue($client->set('name', 'lbx'));

        $this->assertTrue($client->has('name'));

        $this->assertEquals('lbx', $client->get('name'));

        $client->delete('name');

        $this->assertFalse($client->has('name'));

        $originData=['a'=>'aa1', 'b'=>'bb1', 'c'=>'cc1'];
        $this->assertTrue($client->multiSet($originData));

        $this->assertTrue(0==count(array_diff($originData, $client->multiGet(['a', 'b', 'c']))));
    }


    public function testScan()
    {
        /**
         * @var RedisAdapter
         */
        $client=CacheItemPool::getAdapter();

        $count=0;
        while($count<1000)
        {
            $client->set("aa_$count", $count);
            $count++;
        }

    }


}

