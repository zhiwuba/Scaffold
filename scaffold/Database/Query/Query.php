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

    /**
    *  CRUD
    */
    public  function select()
    {
        return $this;
    }

    public function insert()
    {

    }

    public function update()
    {

    }

    public function delete()
    {

    }

    /**
    *   condition
    */
    public function where()
    {
        return $this;
    }

    public function andWhere()
    {

    }

    public function  orWhere()
    {

    }

    public function orderBy()
    {

    }

    public function groupBy()
    {

    }

    public function skip()
    {

    }

    public function take()
    {

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



}

