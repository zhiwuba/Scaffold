<?php
/*
* This file is part of the Scaffold package.
*
* (c) bingxia liu  <xiabingliu@163.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace App\Models;

use Scaffold\Database\Model\ElasticSearchModel;


class PaintEsModel extends ElasticSearchModel
{
    static $mapping=[
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
