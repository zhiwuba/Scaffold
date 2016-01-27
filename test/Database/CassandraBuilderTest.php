<?php
/*
* This file is part of the Scaffold package.
*
* (c) bingxia liu  <xiabingliu@163.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/


namespace Test\Database;

use Cassandra\Bigint;
use Scaffold\Database\Query\CassandraBuilder;
use Test\TestCase;

class CassandraBuilderTest extends TestCase
{
    public function testModel()
    {

    }

    public function testBuilder()
    {
        $builder=new CassandraBuilder('grades');
        $ret=$builder->where('uid', '=', new \Cassandra\Bigint('1'))->max('grade');
        $this->assertEquals(100, $ret);

        $builder=new CassandraBuilder('grades');
        $ret=$builder->where('uid', '=', new Bigint('1'))->sum('grade');
        $this->assertEquals(384, $ret);

        $builder=new CassandraBuilder('grades');
        $ret=$builder->where('uid', '=', new Bigint('1'))->count();
        $this->assertEquals(4 , $ret);
    }

}



