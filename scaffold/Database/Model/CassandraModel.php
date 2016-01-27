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
use Scaffold\Helper\Utility;

class CassandraModel extends Model
{
    protected static $builderClass=CassandraBuilder::class;

    protected static $columns;

    protected static $parsedColumns;

    protected static $isCounter=false;

    /**
     * @inheritDoc
     */
    public static function instance($attribute)
    {
        $data=static::mappingData($attribute);
        return parent::instance($data);
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        if( static::$isCounter )
        {
            $dirtyData=Utility::arrayExclude($this->getDirtyData(), static::$primaryKey);
            $ret=self::getBuilder()->update()->increment($dirtyData)->where($this->getIdQuery())->execute();
            return $ret;
        }
        else
        {
            return parent::save();
        }
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
                if( count($types)==1 ) {
                    static::$parsedColumns[$name]=current($types);
                }
                else {
                    $key=array_shift($types);
                    static::$parsedColumns[$name]=[$key=>$types];
                }
            }
        }
    }

    protected static function mappingObject(array $data)
    {
        $objects=[];
        foreach($data as $key=>$value) {
            $type=static::getColumns($key);
            $objects[$key]=static::data2object($type, $value);
        }
        return $objects;
    }

    protected static function mappingData(array $objects)
    {
        $data=[];
        foreach($objects as $key=>$value) {
            $data[$key]=static::object2data($value);
        }
        return $data;
    }

    public static function processData($data)
    {
        $data=static::mappingObject($data);
        return $data;
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
        if( $instance instanceof Cassandra\Map ) {
            foreach($values as $key=>$value) {
                $instance->set(static::data2object($instance->keyType(), $key), static::data2object($instance->valueType(),$value));
            }
        }
        else if( $instance instanceof Cassandra\Set || $instance instanceof Cassandra\Collection )
        {
            foreach($values as $value) {
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
     *  type to class
     * @param $type string
     * @return null|string
     */
    protected static function type2class($type)
    {
        $class=null;
        $type=strtolower($type);
        if( $type=='list' ){
            $class='Cassandra\\Collection';
        }
        else if( !in_array($type, ['text', 'varchar', 'counter', 'int'])){
            $class= 'Cassandra\\' . ucfirst($type);
        }
        return $class;
    }

}

