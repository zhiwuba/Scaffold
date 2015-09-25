<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-9-20
 * Time: 下午2:15
 */

require_once '../../vendor/autoload.php';
require_once '../../scaffold/Helper/Utility.php';

class UtilityTest extends PHPUnit_Framework_TestCase
{
    public function testArrayFlatten()
    {
        $array=['a', 'b', 'c', 'd'=>['d1'=>'d1', 'd2'=>'d2', 'dzz'=>'d3'],'e'=>['e1'=>['e11', 'e12'], 'e2'] ];
        $result=\Scaffold\Helper\Utility::arrayFlatten($array);
        var_dump($result);
    }

    public function testArrayDot()
    {
        $array=['a', 'b'=>['b1', 'b2'=>['b3']], 'c', ['d1', 'd2']];
        $result=\Scaffold\Helper\Utility::arrayDot($array);
        var_dump($result);
    }

    public function testSnakeCase()
    {
        $result=\Scaffold\Helper\Utility::snakeCase("testSnakeCase");
        var_dump($result);
    }

}


