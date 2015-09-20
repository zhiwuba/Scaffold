<?php
/**
 * Created by PhpStorm.
 * User: explorer
 * Date: 2015/9/5
 * Time: 11:49
 */

namespace Scaffold\Database\Model;

use Scaffold\Database\Model;
use Scaffold\Database\Query\MongoQuery;

class MongoModel extends Model
{
    public function find()
    {
        $query=new MongoQuery(static::$tableName);
        return $query;
    }

}

