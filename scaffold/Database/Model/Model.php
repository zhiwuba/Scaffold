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

use Scaffold\Database\Query\Builder;
use Scaffold\Database\Query\Where;
use Scaffold\Helper\Utility;

abstract class Model extends \ArrayObject implements \JsonSerializable
{
    /**
    *  @var string
    */
    protected static $tableName;

    /**
    *  @var array
    */
    protected static $primaryKey=[];

    /**
    *  @var string
    */
    protected static $builderClass;

    /**
    * @var array  different from ArrayObject.
     */
    protected $originalData;

    /**
     * @var bool todo
     */
    protected $cache=false;

    public function __construct(array $attribute=[])
    {
        $this->originalData=$attribute;
        parent::__construct($attribute);
    }

    /**
    *   instance with attribute.
     * @param $attribute array
     * @return Model
    */
    public static function instance($attribute)
    {
        if( !empty($attribute) ) {
            $instance=new static($attribute);
            return $instance;
        }
        else {
            return NULL;
        }
    }

    /**
    * get builder.
    * @return \Scaffold\Database\Query\Builder
    */
    public static function find()
    {
        return self::getBuilder()->select();
    }

    /**
    *  get model  by primary key.
     * @return $this
    */
    public static function findById()
    {
        $args=func_get_args();
        if( count($args)==count(static::$primaryKey) && Utility::isNormalArray($args) )
        {
            $key=array_combine(static::$primaryKey, $args);
            return self::getBuilder()->where(self::getIdQuery($key))->fetch();
        }
        else
        {
            throw new \InvalidArgumentException("wrong primary id.");
        }
    }

    /**
    * find by some ids.
    * @return  array
    */
    public static function findByIds()
    {
        $args=func_get_args();
        if( count(static::$primaryKey)==1 )
        {
            $key=static::$primaryKey[0];
            $placeholder=implode(',', array_fill(0, count($args), '?'));
            return self::getBuilder()->where("$key in ($placeholder)", $args)->fetchAll();
        }
        else
        {
            $builder=self::getBuilder();

            foreach($args as $id)
            {
                $key=array_combine(static::$primaryKey, $id);
                $builder->orWhere(self::getIdQuery($key));
            }
            return $builder->fetchAll();
        }
    }


    /**
    * delete model.
    */
    public function delete()
    {
        $ret=self::getBuilder()->delete()->where($this->getIdQuery())->execute();
        return $ret;
    }

    /**
    *  destroy model by id or ids
     * @param int|array
     * @return bool
    */
    public static function destroy()
    {
        $args=func_get_args();
        if( count($args)==count(static::$primaryKey) && Utility::isNormalArray($args) )
        {
            $data=array_combine(static::$primaryKey, $args);
            $ret=self::getBuilder()->delete()->where($data)->execute();
            return $ret;
        }
        else
        {
            throw new \InvalidArgumentException("wrong primary id.");
        }
    }

    /**
    *  save the update or create data.
     * @return bool
    */
    public function save()
    {
        $rawDirtyData=$this->getDirtyData();
        $dirtyData=static::processData($rawDirtyData);

        if( empty($this->originalData) )
        {
            $ret=self::getBuilder()->insert()->values($dirtyData)->execute();
            return $ret;
        }
        else
        {
            $ret=self::getBuilder()->update()->set($dirtyData)->where($this->getIdQuery())->execute();
            return $ret;
        }
    }

    /**
     * Determine if the model or given attribute(s) have been modified.
     *
     * @return bool
     */
    public function isDirty()
    {
        return !Utility::isSubSet($this->getArrayCopy() ,$this->originalData);
    }

    public function getDirtyData()
    {
        $dirtyData=[];
        foreach($this->getArrayCopy() as $key=>$value)
        {
            if( !array_key_exists($key, $this->originalData) || $this->originalData[$key]!==$value )
            {
                $dirtyData[$key]=$value;
            }
        }
        return $dirtyData;
    }


    /**
    *  get query object.
    * @return Builder
    */
    public static function getBuilder()
    {
        /** @var Builder $query */
        $query=new static::$builderClass(static::$tableName);
        $query->setModel(get_called_class());
        return $query;
    }


    /**
     * get primary id query.
     * @param $keys array
     * @return Where
    */
    public function getIdQuery(array $keys=[])
    {
        if( empty($keys) )
        {
            $copy=self::getArrayCopy();
            $data=array_merge($this->originalData, $copy);
            $keys=Utility::arrayPick($data, static::$primaryKey);
        }

        $keys=static::processData($keys);

        $where=new Where();
        foreach($keys as $key=>$value) {
            $where->andWhere($key, '=', $value);
        }
        return $where;
    }

    /**
     * pre process some data
     *
     * @param $data
     * @return mixed
     */
    public static function processData($data)
    {
        return $data;
    }

    /**
    * to json.
    */
    public function jsonSerialize()
    {
        $copy=$this->getArrayCopy();
        $data=array_merge($copy, $this->originalData);
        return $data;
    }

}