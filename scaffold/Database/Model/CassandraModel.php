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

use Scaffold\Database\Query\CassandraBuilder;

class CassandraModel extends Model
{
    protected static $builderClass=CassandraBuilder::class;

    protected static $columns;

    public static function getColumns()
    {
        return static::$columns;
    }
}

