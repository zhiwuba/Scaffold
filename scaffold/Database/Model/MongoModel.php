<?php
/**
 * Created by PhpStorm.
 * User: explorer
 * Date: 2015/9/5
 * Time: 11:49
 */

namespace Scaffold\Database\Model;

use Scaffold\Database\Model;
use Scaffold\Database\Query\MongoBuilder;

class MongoModel extends Model
{
    public function find()
    {
        $query=new MongoBuilder(static::$tableName);
        return $query;
    }

}

