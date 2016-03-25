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

use Scaffold\Database\Query\WhereTrait;
use Scaffold\Helper\Utility;

class MysqlBuilder extends Builder
{
    use WhereTrait;

	/**
	 * @var  \Scaffold\Database\Connector\MysqlConnector
	 */
	protected static $connector;

	/**
	 * @var \PDO
	 */
	protected static $connection;

	/**
	 * @var \Scaffold\Database\Profile\Profile
	 */
	protected static $profile;

    /**
     * @var array
     */
    protected $joins=[];

    /**
    *  @var string . TODO array
    */
    protected $haves;

    /**
     *  @var int.  nesting transaction.
     */
    protected static $transactionCounter=0;

    /**
     * choose connection
    *  @param string $name
     * @return $this
    */
    public function choose($name)
    {
		$connection=static::$connector->getConnection($name);
        $this->setConnection($connection);
        return $this;
    }

	public static function getConnector()
	{
		return static::$connector;
	}

	/**
	 *  set connector
	 * @param $connector \Scaffold\Database\Connector\MysqlConnector
	 */
	public static function setConnector($connector)
	{
		static::$connector = $connector;
		static::$connection=$connector->getConnection();
	}

	/**
	 * @return  \PDO
	 */
	public static function getConnection()
	{
		return static::$connection;
	}

	/**
	 *  set connection
	 * @param \Scaffold\Database\Connector\Connector $connection
	 */
	public static function setConnection($connection)
	{
		static::$connection=$connection;
	}

    /**
     *  Cascade operate
     */
    public function join($table, $where)
    {
        $this->joins[]="JOIN $table ON $where";
        return $this;
    }

    public function leftJoin($table, $where)
    {
        $this->joins[]="LEFT JOIN $table ON $where";
        return $this;
    }

    public function rightJoin($table, $where)
    {
        $this->joins[]="RIGHT JOIN $table ON $where";
        return $this;
    }

    public function fullJoin($table, $where)
    {
        $this->joins[]="FULL JOIN $table ON $where";
        return $this;
    }

    public function union($table, $where)
    {

    }

    /**
    *
     */
    public function having($condition)
    {
        $this->haves=$condition;
        return $this;
    }

    /**
     *  aggregation
     */
    public function count()
    {
        return $this->aggressive("count(*)");
    }

    public function max($column)
    {
        return $this->aggressive("max($column)");
    }

    public function min($column)
    {
        return $this->aggressive("min($column)");
    }

    public function sum($column)
    {
        return $this->aggressive("sum($column)");
    }

    /**
     * TODO
     */
    public function avg($column)
    {
        return 0;
    }

    protected function aggressive($aggExp)
    {
        $this->restrictScenario('select');

        $this->selects=[$aggExp];
        list($sql, $params)=$this->assemble();
        $stm=static::getConnection()->prepare($sql);
        $stm->execute($params);
        $result=current($stm->fetch());
        return $result;
    }

    public function lastInsertId()
    {
        return static::getConnection()->lastInsertId();
    }

    /**
     *  trigger
     */
    public function execute()
    {
        if( in_array($this->scenario, ['insert', 'update', 'delete']) )
        {
            list($sql, $bindings)=$this->assemble();
            $sth=static::getConnection()->prepare($sql);
            $ret=$sth->execute($bindings);
            return $ret;
        }
        else
        {
            throw new \Exception("execute only support delete、update and insert!");
        }
    }

    public function fetch()
    {
        if( in_array($this->scenario, ['select']) )
        {
            list($sql, $params)=$this->assemble();
            $stm=static::getConnection()->prepare($sql);
            $stm->execute($params);
            $data=$stm->fetch(\PDO::FETCH_ASSOC);
            if( !empty($this->model) )
            {
                return call_user_func("$this->model::instance", $data);
            }
            return $data;
        }
        else
        {
            throw new \Exception("fetchRow only support select.");
        }
    }

    public function fetchAll()
    {
        if( in_array($this->scenario, ['select']) )
        {
            list($sql, $params)=$this->assemble();
            $stm=static::getConnection()->prepare($sql);
            $stm->execute($params);
            $data=$stm->fetchAll(\PDO::FETCH_ASSOC);
            if( !empty($this->model) )
            {
                $models=array_map(function($item){
                    return call_user_func("$this->model::instance", $item);
                }, $data);
                return $models;
            }
            return $data;
        }
        else
        {
            throw new \Exception("fetchAll only support select.");
        }
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
            $sql='SELECT ' . implode(',' , $selects);
        }else {
            $sql='SELECT *';
        }

        $sql .= " FROM {$this->table} ";

        if( !empty($this->joins) ) {
            $sql .= implode(' ', $this->joins);
        }

        list($whereExp, $whereParams)=$this->assembleWhere($this->where);
        if( !empty($whereExp) ) {
            $sql .= " WHERE $whereExp";
            $bindings=array_merge($bindings, $whereParams);
        }

        if( !empty($this->groups) ) {
            $sql .= " GROUP BY " . implode(',' , $this->groups);
        }

        if( !empty($this->haves) ) {
            $sql .= ' HAVING ' . $this->haves;
        }

        if(!empty($this->orders) ) {
            $orders=[];
            foreach($this->orders as $field=>$order) {
                $orders[]="$field $order";
            }
            $sql .= ' ORDER BY ' . implode(',' , $orders);
        }

        if( !empty($this->take) ){
            if( !empty($this->skip) ){
                $sql .= " LIMIT $this->skip,$this->take";
            }else{
                $sql .= " LIMIT $this->take";
            }
        }

        return array($sql, $bindings);
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

        foreach($this->increments as $key=>$value)
        {
            if( $value>0 )
                $pairs[]="$key=$key+$value";
            elseif($value<0)
                $pairs="$key=$key$value";
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

    /**
     *  transaction.
     * @param $callback \Closure
     * @return mixed
     */
    public static function transaction(\Closure $callback)
    {
        try
        {
            static::setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            static::beginTransaction();
            $callback();
            static::commit();
            return ['ret'=>1];
        }
        catch(\PDOException $e)
        {
            static::rollBack();
            return ['ret'=>0, 'error'=>$e->getMessage()];
        }
    }

    public static function setAttribute($key, $value)
    {
        return static::getConnection()->setAttribute($key, $value);
    }

    public static function beginTransaction()
    {
        if( !static::$transactionCounter++ )
            return static::getConnection()->beginTransaction();
        return static::$transactionCounter >= 0;
    }

    public static function commit()
    {
        if( !--static::$transactionCounter )
            return static::getConnection()->commit();
        return static::$transactionCounter>=0;
    }

    public static function rollBack()
    {
        if( static::$transactionCounter>=0 ) {
            static::$transactionCounter=0;
            return static::getConnection()->rollBack();
        }
        static::$transactionCounter=0;
        return false;
    }
}