<?php
/**
 * User: liubingxia
 * Date: 15-8-7
  */

namespace Scaffold\Database;

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
    *  @var Array
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

    /**
    * @var string
     */
    protected $scenario='create';


    public function __construct($attribute=[])
    {
        $this->data=$attribute;
    }

    /**
    *   instance with attribute.
    */
    public static function instance($attribute)
    {
        $instance=new static($attribute);
        return $instance;
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
    */
    public static function findById()
    {
        $args=func_get_args();
        if( count($args)==count(static::$primaryKey) && Utility::isNormalArray($args) )
        {
            $key=array_combine(static::$primaryKey, $args);
            return self::getBuilder()->where($key)->fetchRow();
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
                $condition=array_combine(static::$primaryKey, $id);
                $builder->orWhere($condition);
            }
            return $builder->fetchAll();
        }
    }


    /**
    * delete model.
    */
    public function delete()
    {
        $ret=self::getBuilder()->delete()->where($this->getPrimaryIdQuery())->execute();
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
        if( $this->scenario=='create' )
        {
            $ret=self::getBuilder()->insert()->values($modify)->execute();
            return $ret;
        }
        else if( $this->scenario=='update' )
        {
            if( !Utility::isSubSet($modify ,$this->data) )
            {
                $ret=self::getBuilder()->update()->set($modify)->where($this->getPrimaryIdQuery())->execute();
                return $ret;
            }
        }
    }

    /**
    *  get query object.
    * @return Builder
    */
    public function getBuilder()
    {
        $query=new static::$builderClass(static::$tableName);
        $query->setModel(get_called_class());
        return $query;
    }

    /**
    *  @return Where
     * @throws \Exception
    */
    private function getPrimaryIdQuery()
    {
        $where=new Where();
        foreach(static::$primaryKey as $key)
        {
            if( isset($this->data[$key]) )
            {
                $where->andWhere("$key=?", $this->data[$key]);
            }
            else
            {
                throw new \Exception("can't find primary key.");
            }
        }
        return $where;
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