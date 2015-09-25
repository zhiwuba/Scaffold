<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午3:08
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
        else if( Utility::isAssocArray($args) )
        {
            $this->data=array_merge($this->data, $args);
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
        else if( Utility::isAssocArray($args) )
        {
            $this->data=array_merge($this->data, $args );
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
     * select()->orWhere(function($query){ $query->where()->where()})->orWhere();
     * select()->where()->where(function($query){$query->orWhere()->orWhere()});
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
            call_user_func_array([$this->where, 'andWhere'], func_get_args());
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
