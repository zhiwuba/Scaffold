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

use Scaffold\Cache\CacheItem;
use Scaffold\Cache\CacheItemPool;

class Dog
{
    protected $name;
    protected $sex;
    protected $children=[];


    public function setStatus()
    {
        $this->name='len';
        $this->sex='boy';
    }

    /**
     * @inheritDoc
     */
    function __sleep()
    {
        $this->setStatus();
        return ['name', 'sex', 'children'];
    }
}


class CacheItemPoolTest extends \Test\TestCase
{
    public function testBaseOperate()
    {
        $pool=new CacheItemPool();

        $pool->saveDeferred(new CacheItem('a', 'aa'));
        $pool->saveDeferred(new CacheItem('b', 'bb'));
        $pool->commit();

        $dog=new Dog();
        $pool->save(new CacheItem('dog', $dog));

        $dog2=$pool->getItem('dog');
        //$this->assertEquals($dog, $dog2);
    }

    public function testGetItems()
    {
        $pool=new CacheItemPool();

        $items=$pool->getItems(['aa_7', 'aa_0', 'aa_3']);

        foreach($items as $key=>$value)
        {
            echo $key, '===' ,$value->getValue(), PHP_EOL;
        }
    }

}