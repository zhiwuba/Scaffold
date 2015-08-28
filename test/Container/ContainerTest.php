<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-14
 * Time: 下午9:08
 */

class ContainerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        echo "EnterInto setUp\n";
        parent::setUp();
    }

    public function testContainer()
    {
        $this->assertEquals([1,2,3], [1,2,3]);
    }

    protected function tearDown()
    {
        echo "EnterInto tearDown\n";
        parent::tearDown();
    }

}
