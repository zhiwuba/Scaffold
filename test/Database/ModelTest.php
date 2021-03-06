<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-9-24
 * Time: 下午9:00
 */
namespace Test\Database;

use Scaffold\Database\Model\MysqlModel;
use Scaffold\Database\Connector\MysqlConnector;
use \Scaffold\Database\Query\MysqlBuilder;
use Test\TestCase;

class UserPostModel extends MysqlModel
{
    protected static $tableName='yws_user_posts';
    protected static $primaryKey=['user_id' ,'channel_id' ,'post_sid'];

}


class ModelTest extends TestCase
{
    public function testCreateModel()
    {
        $model=new UserPostModel();
        $model['user_id']=1;
        $model['post_sid']=1;
        $model['channel_id']=1;
        $model['thread_sid']=1;
        $model->save();
    }

    public function testUpdateModel()
    {
       $model=UserPostModel::findById(1,1,1);
       $model['thread_sid']='2';
       $model->save();
    }

    public function testDeleteModel()
    {
       $model=UserPostModel::findById(1,1,1);
        if( $model!==NULL )
        {
            $model->delete();
        }
    }
}
