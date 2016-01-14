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

use Cassandra;

/**
 * TODO: batch execute
 * TODO: fetch
 * Class CassandraBuilder
 * @package Scaffold\Database\Query\
 */
class CassandraBuilder extends Builder
{
    use WhereTrait;

    /**
     * @var \Cassandra\Session
     */
	public static $connection;

	public static $profile;

    /**
     * @var array
     */
    protected $options=[];

	public static function getConnection()
	{
		return static::$connection;
	}

	/**
	 * @param \Cassandra\Session $connection
	 */
	public static function setConnection($connection)
	{
		static::$connection=$connection;
	}

    public function withOptions($key, $values)
    {
        $this->options[$key]=$values;
        return $this;
    }

    /**
     *  trigger
     */
    public function execute()
    {
        $this->restrictScenario(['insert', 'update', 'delete']);

        list($cql, $bindings)=$this->assemble();
        $statement=static::getConnection()->prepare($cql);
        $options=new Cassandra\ExecutionOptions(
            ['arguments'=>$bindings]+$this->options
        );
        $rows=static::getConnection()->execute($statement, $options);
        return $rows;
    }

    public function fetch()
    {
        $this->restrictScenario('select');

        list($sql, $params)=$this->assemble();
        $statement=static::getConnection()->prepare($sql);
        $options=new Cassandra\ExecutionOptions(
            ['arguments'=>$params]+$this->options
        );

        $rows=static::getConnection()->execute($statement,$options);
        if( !empty($this->model) )
        {
            $row=$rows->first()?: [];
            return call_user_func([$this->model, 'instance'], $row);
        }
        return $rows;
    }

    public function fetchAll()
    {
        $this->restrictScenario('select');

        list($sql, $params)=$this->assemble();
        $statement=static::getConnection()->prepare($sql);
        $options=new Cassandra\ExecutionOptions(
            ['arguments'=>$params]+$this->options
        );

        $rows=static::getConnection()->execute($statement,$options);
        if( !empty($this->model) )
        {
            $models=[];
            foreach($rows as $row)
            {
                $models[]=call_user_func([$this->model, 'instance'], $row);
            }
            return $models;
        }
        return $rows;
    }

    /**
     *  aggregation
     */
    public function count()
    {
        $this->restrictScenario('select');

    }

    public function max($column)
    {

    }

    public function min($column)
    {

    }

    public function sum($column)
    {

    }

    protected function assembleSelect()
    {
        $bindings=[];
        if( !empty($this->selects) ) {
            $selects=array_map(function($select){
                if( is_array($select) ){
                    return key($select) . " AS " . current($select);
                }else{
                    return $select;
                }
            }, $this->selects);
            $cql='SELECT ' . implode(',' , $selects);
        }else {
            $cql='SELECT *';
        }

        $cql .= " FROM {$this->table} ";

        if( !empty($this->joins) ) {
            $cql .= implode(' ', $this->joins);
        }

        list($whereExp, $whereParams)=$this->assembleWhere($this->where);
        if( !empty($whereExp) ) {
            $cql .= " WHERE $whereExp";
            $bindings=array_merge($bindings, $whereParams);
        }

        if(!empty($this->orders) ) {
            $orders=array_map(function($item){
                return $item[0] . ' ' . $item[1];
            }, $this->orders);
            $cql .= ' ORDER BY ' . implode(',' , $orders);
        }

        if( !empty($this->take) ){
            $cql .= " LIMIT $this->take";
        }

        return array($cql, $bindings);
    }

    protected function assembleInsert()
    {
        $keys=implode(',', array_keys($this->data));
        $bindings=array_values($this->data);
        $placeholder=implode(',', array_fill(0, count($bindings), '?'));
        $sql="INSERT INTO {$this->table} ($keys) VALUES($placeholder)";
        return array($sql, $bindings);
    }

    protected function assembleUpdate()
    {
        $pairs=[];
        $bindings=[];
        foreach( $this->data as $key=>$value )
        {
            $pairs[] =$key . '=?';
            $bindings[]=$value;
        }

        list($where, $params)=$this->assembleWhere($this->where);
        if( !empty($where) ) {
            $bindings=array_merge($bindings, $params);
        }else{
            throw new \Exception("where must not be null in update.");
        }

        $sql="UPDATE {$this->table} SET " . implode(',', $pairs) . " WHERE $where";
        return array($sql, $bindings);
    }

    protected function assembleDelete()
    {
        list($where, $bindings)=$this->assembleWhere($this->where);
        if( empty($where) ){
            throw new \Exception("where must not be null in delete.");
        }

        $sql="DELETE FROM {$this->table} WHERE $where";
        return array($sql, $bindings);
    }


}