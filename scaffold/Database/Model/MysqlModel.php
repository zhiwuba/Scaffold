<?php
/**
 * Created by PhpStorm.
 * User: explorer
 * Date: 2015/8/31
 * Time: 0:20
 */

namespace Scaffold\Database\Model;

use Scaffold\Database\Model;

abstract class MysqlModel extends Model
{
    protected static $builderClass='\Scaffold\Database\Query\MysqlQuery';


}
