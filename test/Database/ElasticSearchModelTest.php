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

use Test\TestCase;
use Scaffold\Database\Model\ElasticSearchModel;

class PaintModel extends ElasticSearchModel
{
    public static $tableName='paints';

    public static $primaryKey=['id'];

    public static $index='gallery';

    public static $routingKey='id';

    public static $mapping=[
        'settings'=>[
            'number_of_shards'=>1,
            'number_of_replicas'=>1
        ],
        'mappings'=>[
            'paints'=>[
                'properties'=>[
                    'id'=>[
                        'type'=>'integer'
                    ],
                    'name'=>[
                        'type'=> 'string',
                        'index'=>'not_analyzed'
                    ],
                    'filename'=>[
                        'type'=>'string',
                        'index'=>'not_analyzed'
                    ],
                    'author'=>[
                        'type'=>'string',
                        'index'=>'not_analyzed'
                    ],
                    'mark'=>[
                        'type'=>'string',
                        'index'=>'analyzed'
                    ],
                    'created_at'=>[
                        'type'=>'date'
                    ],
                    'comments'=>[
                        'type'=>'integer',
                    ],
                    'likes'=>[
                        'type'=>'integer'
                    ]
                ]
            ]
        ]
    ];
}

class ElasticSearchModelTest extends TestCase
{
    public function NtestNew()
    {
    }

    public function NtestUpdate()
    {
        $paint=PaintModel::findById(1);
        $paint['name']='mahua4';
        $paint->increment('likes', 100);
        $paint->decrement('comments', 1);
        $paint->save();
    }

    public function NtestFind()
    {
        $body=PaintModel::query()->andWhere('filename', '=', 'mahua4')->andWhere('comments', '>=', '7')->assemble();
        var_dump($body);
    }

    public function NtestDelete()
    {
        PaintModel::destroy(1);
    }

    public function testWhere()
    {
        $paints=PaintModel::query()->andWhere('likes', '>=',  5000)->andWhere('name', '=', 'kim')->orderBy('likes')->fetchAll();
        foreach($paints as $paint)
        {
            echo $paint['name'], '  ',  $paint['likes'], "\n";
        }
        echo "=====end=====\n";
    }

    public function testGroup()
    {
        $paints=PaintModel::query()->andWhere('name', '=', 'kim')->groupBy('name')->groupBy('author')->fetchAll();
        foreach($paints as $paint)
        {
            echo $paint['name'], '  ',  $paint['likes'], "\n";
        }
        echo "====end====\n";
    }

    public function testMinMaxSum()
    {
        $ret=PaintModel::query()->groupBy('name')->sum('likes');
        //print_r($ret);

        $ret=PaintModel::query()->groupBy('name')->groupBy('author')->min('likes');
        //print_r($ret);

        $ret=PaintModel::query()->groupBy('name')->groupBy('author')->max('likes');
        //print_r($ret);

        $ret=PaintModel::query()->groupBy('name')->groupBy('author')->avg('likes');
        //print_r($ret);

        $ret=PaintModel::query()->avg('likes');
        //print_r($ret);
    }

    public function testCount()
    {
        $ret=PaintModel::query()->andWhere('name', '=', 'kim')->count();
        echo $ret, "\n";
    }

    public function testQuery()
    {

    }
}
