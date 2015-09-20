<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午7:41
 */

namespace Scaffold\Database;

abstract class Model extends \ArrayObject
{
    protected static $tableName;
    protected static $primaryKey;
    protected static $queryType;

    /**
    * @var array
     */
    protected $data;

    /**
    * @var string
     */
    protected $scenario='create';


    public static function create()
    {
        $instance=new static();
        $instance->scenario='create';
        return $instance;
    }

    /**
    * find
    */
    public abstract function find();

    public function delete()
    {

    }

    public function save()
    {

    }

    public function __get($name)
    {
        if( isset($this->data[$name]) )
        {
            return $this->data[$name];
        }
        else
        {
            return null;
        }
    }

    public function __set($name, $value)
    {
        $this->data[$name]=$value;
    }

    public function offsetExists($index)
    {
        parent::offsetExists($index);
    }

    public function offsetGet($index)
    {
        parent::offsetGet($index);
    }

    public function offsetSet($index, $value)
    {
        parent::offsetSet($index, $value);
    }

    public function offsetUnset($index)
    {
        parent::offsetUnset($index);
    }
}