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

use Scaffold\Database\Model\CassandraModel;
use Test\TestCase;

class UserModel extends CassandraModel
{
    protected static $primaryKey=['user_id'];

    protected static $tableName='users';

    protected static $parsedColumns;

    protected static $columns=[
        'user_id'=>      'bigint',
        'user_name'=>'varchar',
        'user_meta'=>  'map<varchar,varchar>',
        'user_friends'=>'list<varchar>',
        'user_phones'=>'set<varint>',
        'user_ip'       =>  'inet',
        'user_age'     => 'int',
        'last_login'   =>  'timestamp',
        'password'  => 'text',
        'grade'        => 'list<map<varchar,float>>'
    ];
}

class PaintCounter extends CassandraModel
{
    protected static $primaryKey=['paint_id'];

    protected static $tableName='paint_counter';

    protected static $parsedColumns;

    protected static $isCounter=true;

    protected static $columns=[
        'paint_id'  => 'bigint',
        'comment_count'=>'counter'
    ];
}


class CassandraModelTest extends TestCase
{
    public function testNewModel()
    {
        $user=new UserModel();
        $user['user_id']=14;
        $user['user_name']='liubingxia';
        $user['user_age']=25;
        $user['user_friends'][]='friend1';
        $user['user_friends'][]='friend2';
        $user['user_friends'][]='friend3';
        $user['user_meta']['title']='enginer';
        $user['user_meta']['work']='it';
        $user['user_phones'][]=15801550437;
        $user['user_phones'][]=13141253537;
        $user->save();
    }

    public function testUpdateModel()
    {
        $user=UserModel::findById(13);
        $user['user_age']=$user['user_age']+1;
        $user['user_friends'][]='friend4';
        $user['user_meta']['private']='count of girl friends';
        $user->save();
    }

    public function testDeleteModel()
    {
        $user=UserModel::find()->where('user_id', '=', 12)->fetch();
        $user->delete();
    }

    public function testSearchModel()
    {
        $user=UserModel::findById(12);
        var_dump($user);
    }

    public function testCounter()
    {
        $counter=new PaintCounter();
        $counter['paint_id']=1;
        $counter['comment_count']=2;
        $counter['comment_count']=-1;
        $counter->save();
    }



}