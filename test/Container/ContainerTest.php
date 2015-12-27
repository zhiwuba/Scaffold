<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-14
 * Time: 下午9:08
 */

namespace Test\Container;

use Scaffold\Helper\Container;
use Test\TestCase;

class Object
{
    /**
     * Object constructor.
     */
    public function __construct()
    {
        echo "construct";
    }

    public $name='object';
}



class ContainerTest extends TestCase
{
    public function testSingleton()
    {
        $container=new Container();

        $container->singleton('object', function(){
            return new Object();
        });

        $object=$container->get('object');
        $this->assertEquals('object', $object->name);

        $object->name='new-object';

        $object2=$container->get('object');
        $this->assertEquals('new-object', $object2->name);
    }

}
