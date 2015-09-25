<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-9-21
 * Time: 下午6:06
 */

require_once '../../vendor/autoload.php';
require_once '../../scaffold/Database/Query/MysqlBuilder.php';


class MysqlQueryTest extends PHPUnit_Framework_TestCase
{
    public function testMysql()
    {
        $query=new \Sacaffold\Database\Query\MysqlQuery("paint");
        $ret=$query
            ->select()
            ->leftJoin('comment', "comment.id=paint.id")
            ->leftJoin('user', "user.user_id=paint.user_id")
            ->orwhere(function($query){
                $query
                    ->andWhere('user_id>?', 10)
                    ->andWhere('user_id<?', 20);
            })
            ->orwhere(function($query){
                $query
                    ->andWhere('paint_id>?', 77)
                    ->andWhere('paint_id<?', 199);
            })
            ->groupBy('type')
            ->orderBy('paint_id')
            ->skip(10)
            ->take(20)
            ->assemble();

        print_r($ret);
    }

    public function testMysql2()
    {

    }

}
