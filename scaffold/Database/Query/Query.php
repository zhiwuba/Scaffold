<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午3:08
 */

namespace Scaffold\Database\Model;

abstract class Query
{
    const SORT_ASC='asc';
    const SORT_DESC='desc';

    protected $scenario;
    protected $selects=[];
    protected $wheres=[];
    protected $orders=[];
    protected $groups=[];
    protected $skip=0;
    protected $take=0;

    protected $join;

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
    *   condition
    */
    public function where()
    {
        $args=func_get_args();
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

    public function orderBy($field,$order)
    {
        $args=func_get_args();
        if( $args[0] )
        {

        }
    }

    public function groupBy()
    {

    }

    public function skip($offset)
    {
        $this->skip=$offset;
    }

    public function take($take)
    {
        $this->take=$take;
    }

    /**
    *  Cascade
    */
    public function join()
    {

    }

    public function leftJoin()
    {

    }

    public function rightJoin()
    {

    }

    public function fullJoin()
    {

    }

    public function union()
    {

    }



    /**
    *  trigger
    */
    public function fetch()
    {

    }

    public function fetchRow()
    {

    }

    public function fetchPair()
    {

    }

    public function fetchGroup()
    {

    }

    public function fetchAll()
    {

    }

    /**
    *  aggregation
    */
    abstract public function count();

    abstract public function max();

    abstract public function min();

    abstract public function sum();


    /**
    *  原生的语句
     */
    abstract public function assemble();

    abstract public function execute();

}

