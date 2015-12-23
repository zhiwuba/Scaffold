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
    protected $data;


    public function __construct($attribute=[])
    {
        $this->data=$attribute;
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
            return self::getBuilder()->where(self::getIdQuery($key))->fetchRow();
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
        $modify=$this->getArrayCopy();
        if( empty($this->data) )
        {
            $ret=self::getBuilder()->insert()->values($modify)->execute();
            return $ret;
        }
        else
        {
            if( !Utility::isSubSet($modify ,$this->data) )
            {
                $ret=self::getBuilder()->update()->set($modify)->where($this->getIdQuery())->execute();
                return $ret;
            }
        }
    }

    /**
    *  get query object.
    * @return Builder
    */
    public static function getBuilder()
    {
        $query=new static::$builderClass(static::$tableName);
        $query->setModel(get_called_class());
        return $query;
    }

    /**
     * get primary id query.
     * @param $data array
     * @return Where
     * @throws \Exception
    */
    private static function getIdQuery(array $data=[])
    {
        if( empty($data) ) {
            $data=&self::$data;
        }

        $where=new Where();
        foreach(static::$primaryKey as $key) {
            if( isset($data[$key]) ) {
                $where->andWhere($key, '=', $data[$key]);
            }
            else {
                throw new \Exception("can't find primary key.");
            }
        }
        return $where;
    }

    /**
    * to json.
    */
    public function jsonSerialize()
    {
        $copy=$this->getArrayCopy();
        $data=array_merge($copy, $this->data);
        return $data;
    }

    public function offsetExists($index){
        parent::offsetExists($index);
    }
    public function offsetGet($index){
        parent::offsetGet($index);
    }
    public function offsetSet($index, $value){
        parent::offsetSet($index, $value);
    }
    public function offsetUnset($index){
        parent::offsetUnset($index);
    }
}