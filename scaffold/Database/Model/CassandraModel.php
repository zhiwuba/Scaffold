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

use Cassandra;
use Cassandra\Type;
use Scaffold\Database\Query\CassandraBuilder;

class CassandraModel extends Model
{
    protected static $builderClass=CassandraBuilder::class;

    protected static $columns;

    protected static $parsedColumns;

    /**
     * @inheritDoc
     */
    public static function instance($attribute)
    {
        $data=[];
        foreach($attribute as $key=>$value)
        {
            $data[$key]=static::object2data($value);
        }
        return parent::instance($data);
    }

    public static function getColumns($key)
    {
        static::parseColumns();
        return static::$parsedColumns[$key];
    }

    protected static function parseColumns()
    {
        if( empty(static::$parsedColumns) )
        {
            foreach(static::$columns as $name=>$struct)
            {
                $types=explode(',' , trim(str_replace('>',  ',' , str_replace('<',  ',' ,$struct)), ','));
                if( count($types)==1 )
                {
                    static::$parsedColumns[$name]=current($types);
                }
                else
                {
                    $key=array_shift($types);
                    static::$parsedColumns[$name]=[$key=>$types];
                }
            }
        }
    }

    protected function getNewValues()
    {
        $newValues=[];
        $copy=$this->getArrayCopy();
        foreach($copy as $key=>$value)
        {
            $type=static::getColumns($key);
            $newValues[$key]=static::data2object($type, $value);
        }
        return $newValues;
    }

    /**
     *  data to object
     *
     * @param $type
     * @param $value
     * @return object
     */
    protected static function data2object($type, $value)
    {
        if( is_string($type) )
        {
            $class=static::type2class($type);
            if( !empty($class) )
                return new $class($value);
            else
                return $value;
        }
        elseif( is_array($type) )
        {
            $class=static::type2class(key($type));
            $elemType = current($type);

            $rc = new \ReflectionClass($class);
            if (is_array($elemType)) {
                $object = $rc->newInstanceArgs($elemType);
            } else {
                $object = $rc->newInstance($elemType);
            }

            static::fillObject($object, $value);
            return $object;
        }
    }


    /**
     *  fill object with values
     *
     * @param $instance
     * @param $values
     */
    protected static function fillObject($instance, $values)
    {
        if( $instance instanceof Cassandra\Map )
        {
            foreach($values as $key=>$value)
            {
                $instance->set(static::data2object($instance->keyType(), $key), static::data2object($instance->valueType(),$value));
            }
        }
        else if( $instance instanceof Cassandra\Set || $instance instanceof Cassandra\Collection )
        {
            foreach($values as $value)
            {
                $instance->add(static::data2object($instance->type(),$value));
            }
        }
    }


    /**
     *  object to data.
     *
     * @param $value
     * @return array
     */
    protected static function object2data($value)
    {
        if( $value instanceof Cassandra\Map )
        {
            $keys=array_map(function($key){ return static::object2data($key);}, $value->keys());
            $values=array_map(function($value){ return static::object2data($value);}, $value->values());

            return array_combine($keys , $values);
        }
        else if($value instanceof Cassandra\Collection || $value instanceof Cassandra\Set)
        {
            return array_map(function($value){return static::object2data($value);}, $value->values());
        }
        else if(is_object($value))
        {
            return $value->value();
        }
        else
        {
            return $value;
        }
    }


    /**
     *  type 2 class
     *
     * @param $type string
     * @return null|string
     */
    protected static function type2class($type)
    {
        $type=strtolower($type);
        if( $type=='list' )
            return 'Cassandra\\Collection';
        else if( !in_array($type, ['text', 'varchar']))
            return 'Cassandra\\' . ucfirst($type);
        else
            return null;
    }

}

