<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scaffold\Database\Query;

use Scaffold\Helper\Utility;

abstract class Builder
{
    protected static $sortOrder=[
        SORT_ASC=>'asc',
        SORT_DESC=>'desc'
    ];

    protected $scenario='select';

    protected $table;

    protected $model;

    protected $selects=[];

    /**
     * @var Where
     */
    protected $where;

    protected $orders=[];

    protected $groups=[];

    protected $skip=0;

    protected $take=0;

    protected $data=[];

    protected $bindings=[];

    public function __construct($tableName)
    {
        $this->table=$tableName;
        $this->where=new Where();
    }

	public static function setProfile($profile)
	{
		static::$profile=$profile;
	}

	public static function getProfile()
	{
		return static::$profile;
	}

    /**
    *  CRUD
    */
    public  function select()
    {
        array_merge($this->selects, func_get_args());
        $this->scenario='select';
        return $this;
    }

    public function insert()
    {
        $this->scenario='insert';
        return $this;
    }

    public function update()
    {
        $this->scenario='update';
        return $this;
    }

    public function delete()
    {
        $this->scenario='delete';
        return $this;
    }

    /**
     *  update set
     * @usage: set(a, b)  set([a=>b, c=>d])
     */
    public function set()
    {
        $args=func_get_args();
        if ( Utility::isNormalArray($args) && count($args)==2 )
        {
            $this->data[$args[0]]=$args[1];
        }
        else if( Utility::isAssocArray($args[0]) )
        {
            $this->data=array_merge($this->data, $args[0]);
        }
        else
        {
            throw new \Exception("unsupported arguments");
        }
        return $this;
    }

    /**
    *  insert into.
    */
    public function values()
    {
        $args=func_get_args();
        if( Utility::isNormalArray($args) && count($args)==2 )
        {
            $this->data[$args[0]] = $args[1];
        }
        else if( Utility::isAssocArray($args[0]) )
        {
            $this->data=array_merge($this->data, $args[0]);
        }
        else
        {
            throw new \Exception("unsupported arguments.");
        }
        return $this;
    }

    /**
    *   condition
     * select()->where()->where();
     * select()->andWhere()->andWhere();
     * select()->orWhere(function(){ $this->where()->where()})->orWhere();
     * select()->where()->where(function(){$this->orWhere()->orWhere()});
    */
    public function where()
    {
        $args=func_get_args();
        if( count($args)==1 && $args[0] instanceof Where)
        {
            $this->where=$args[0];
        }
        else
        {
            call_user_func_array([$this->where, 'andWhere'], $args);
        }
        return $this;
    }

    public function andWhere()
    {
        call_user_func_array([$this->where, 'andWhere'], func_get_args());
        return $this;
    }

    public function  orWhere()
    {
        call_user_func_array([$this->where, 'orWhere'], func_get_args());
        return $this;
    }

    public function orderBy($field,$order='asc')
    {
        $order=strtolower($order);
        if( in_array($order, ['asc', 'desc']) )
        {
            array_push($this->orders, [$field, $order]);
        }
        return $this;
    }

    public function groupBy($field)
    {
        array_push($this->groups, $field);
        return $this;
    }

    public function skip($offset)
    {
        $this->skip=$offset;
        return $this;
    }

    public function take($take)
    {
        $this->take=$take;
        return $this;
    }

    /**
    *  trigger
    */
    abstract public function execute();

    abstract public function fetch();

    abstract public function fetchRow();

    abstract public function fetchPair();

    abstract public function fetchGroup();

    abstract public function fetchAll();

    /**
    *  aggregation
    */
    abstract public function count();

    abstract public function max($column);

    abstract public function min($column);

    abstract public function sum($column);

    /**
    *  model
     * @param Model $model
    */
    public function setModel($model)
    {
        $this->model=$model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function restrictScenario($scenario)
    {
        if( (is_array($scenario)&&!in_array($this->scenario, $scenario)) ||
            (is_string($scenario)&& $this->scenario!=$scenario))
        {
            throw new BuilderException("operate is forbidden in this scenario");
        }
    }

    /**
    *  assemble fluent query.
     */
    public function assemble()
    {
        if( empty( $this->scenario ) ) {
            throw new \Exception("null scenario.");
        }

        if( $this->scenario=='select' ) {
            return $this->assembleSelect();
        }else if ( $this->scenario=='insert' ) {
            return $this->assembleInsert();
        }else if( $this->scenario=='update' ) {
            return $this->assembleUpdate();
        }else if( $this->scenario=='delete' ) {
            return $this->assembleDelete();
        }
    }

    abstract protected function assembleSelect();

    abstract protected function assembleInsert();

    abstract protected function assembleUpdate();

    abstract protected function assembleDelete();
}
