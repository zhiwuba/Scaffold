<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test;


$configs['elasticsearch']=[
    'hosts'=>['127.0.0.1']
];

$configs['cassandra']=[
    'hosts'=>[
        '127.0.0.1'
    ],
    'port'=>9042,
    'keyspace'=>'gallery',

];

$configs['mysql']=[
    'read' => array(
    ),
    'write' => array(
        'main' => '127.0.0.1',
    ),

    'driver'    => 'mysql',
    'database'  => 'test',
    'username'  => 'root',
    'password'  => 'fengyi',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'      => '',
];

$configs['redis']=[
    'hosts'=>['127.0.0.1']
];

return $configs;