<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-9-21
 * Time: 下午6:06
 */

namespace Test\Database;

use Test\TestCase;

class MysqlQueryTest extends TestCase
{
    public function testMysql()
    {
        $query=new \Scaffold\Database\Query\MysqlQuery("paint");
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
