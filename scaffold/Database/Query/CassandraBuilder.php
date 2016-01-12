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
        $this->restrictScenario('select');

        list($sql, $params)=$this->assemble();
        $statement=static::getConnection()->prepare($sql);
        $options=new Cassandra\ExecutionOptions(
            ['arguments'=>$params]+$this->options
        );
        $rows=static::getConnection()->execute($statement, $options);
        if( !empty($this->model) )
        {
            return call_user_func_array([$this->model, 'instance'], $rows); //TODO
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

    }

    protected function assembleInsert()
    {

    }

    protected function assembleUpdate()
    {

    }

    protected function assembleDelete()
    {

    }

}