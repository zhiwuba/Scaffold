<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scaffold\Database\Model;

use Scaffold\Database\Query\MysqlBuilder;

abstract class MysqlModel extends Model
{
    protected static $builderClass=MysqlBuilder::class;


}
