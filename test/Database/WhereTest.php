<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-9-20
 * Time: 下午4:51
 */



require_once '../../vendor/autoload.php';
require_once '../../scaffold/Database/Query/Where.php';

use Scaffold\Database\Query\Where;

class WhereTest extends PHPUnit_Framework_TestCase {

    public function testWhere()
    {
        $where=new Where(Where::$relationOR);

        $where->orWhere('A', 'a1', 'a2')->orWhere(function($query){
            $query->andWhere('B', 'b1', 'b2')->andWhere('C', ['c1', 'c2']);
        })->orWhere(function($query){
            $query->andWhere('D', 'd1', 'd2')->andWhere(function($query){
                $query->orWhere('E', ['e1', 'e2'])->orWhere('F', 'f1', 'f2');
            });
        });

        list($exp, $values)=$where->assemble();
        echo $exp;

        print_r($values);
    }

}
