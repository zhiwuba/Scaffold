<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-9-24
 * Time: 下午9:00
 */

use Scaffold\Database\Model\MysqlModel;

class ModelTest extends PHPUnit_Framework_TestCase
{
    public function testCreateModel()
    {
        $model=new MysqlModel();
        $model->id='id';
        $model->name='name';
        $model->save();
    }

    public function testUpdateModel()
    {
        $model=MysqlModel::findById();
        $model->name='new-name';
        $model->save();
    }

    public function testDeleteModel()
    {
        MysqlModel::findById();
    }


}
