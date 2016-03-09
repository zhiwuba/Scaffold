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
        'setting'=>[
            'number_of_shared'=>1,
            'number_of_replicas'=>1
        ],
        'mapping'=>[
            'paints'=>[
                'properties'=>[
                    'id'=>[
                        'type'=>'int'
                    ],
                    'name'=>[
                        'type'=> 'string',
                        'index'=>'not_analyzed'
                    ],
                    'filename'=>[
                        'type'=>'string',
                        'index'=>'not_analyzed'
                    ],
                    'created_at'=>[
                        'type'=>'date'
                    ],
                    'comments'=>[
                        'type'=>'int',
                    ],
                    'likes'=>[
                        'type'=>'int'
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
        $names=['john', 'kim', 'david', 'Michael', 'Lily', 'William', 'Peter', 'Rachel', 'Daniel', 'Elizabeth'];
        for($i=2; $i<3000;$i++)
        {
            $paint=new PaintModel();
            $paint['id']=$i;
            $paint['name']= $names[rand(0, count($names)-1)];
            $paint['filename']=$paint['name'] . '\'s file, code is ' . $i;
            $paint['created_at']='2012-02-06';
            $paint['comments']=rand(0,10000);
            $paint['likes']=rand(0, 10000);
            $paint->save();
        }
    }

    public function NtestUpdate()
    {
        $paint=PaintModel::findById(1);
        $paint['name']='mahua4';
        $paint->increment('likes', 100);
        $paint->decrement('comments', 1);
        $paint->save();
    }

    public function testFind()
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

    }

}
