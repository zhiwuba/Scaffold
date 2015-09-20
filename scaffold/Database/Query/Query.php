<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午3:08
 */

namespace Scaffold\Database\Query;

use Scaffold\Helper\Utility;

abstract class Query
{
    protected static $sortOrder=[
        SORT_ASC=>'asc',
        SORT_DESC=>'desc'
    ];

    protected $scenario;

    protected $table;

    protected $selects=[];

    /**
     * @var array
     *                 tree
     *                 and
     *             /          \
     *          or           and
     *        /    \          /   \
     *     a=b  c=d   e=f   or
     *                             /   \
     *                       m=n  p=q
     */
    protected $wheres=[];

    protected $orders=[];

    protected $groups=[];

    protected $skip=0;

    protected $take=0;

    protected $data=[];

    public function __construct($tableName)
    {
        $this->table=$tableName;
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
    }

    public function update()
    {
        $this->scenario='update';
    }

    public function delete()
    {
        $this->scenario='delete';
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
    }

    /**
    *   condition
     * select()->where()->where();
     *  select()->andWhere()->andWhere();
     * select()->orWhere(function($query){ $query->where()->where()})->orWhere();
     * select()->where()->where(function($query){$query->orWhere()->orWhere()});
    */
    public function where()
    {
        $where=[];
        $args=func_get_args();
        $argc=count($args);
        if( $argc==1 && is_callable($args[0]) )
        {

        }
        else if ( $argc==2 )
        {

        }
        else if( $argc==3 )
        {

        }
        return $this;
    }

    public function andWhere()
    {
        $args=func_get_args();

        return $this;
    }

    public function  orWhere()
    {
        $args=func_get_args();

        return $this;
    }

    public function orderBy($field,$order=SORT_ASC)
    {
        array_push($this->orders, [$field, $order]);
        return $this;
    }

    public function groupBy($field)
    {
        array_push($this->groups, $field);
        return $this;
    }

    public function having()
    {
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

    abstract public function max();

    abstract public function min();

    abstract public function sum();


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
