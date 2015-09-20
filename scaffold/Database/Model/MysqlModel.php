<?php
/**
 * Created by PhpStorm.
 * User: explorer
 * Date: 2015/8/31
 * Time: 0:20
 */

namespace Scaffold\Database\Query;

use Sacaffold\Database\Query\MysqlQuery;
use Scaffold\Database\Model;

class MysqlModel extends Model
{
    /**
    * @return \Scaffold\Database\Query\MysqlQuery
    */
    public  function find()
    {
        $query=new MysqlQuery(static::$tableName);
        return $query;
    }


}
