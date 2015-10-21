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

class MysqlBuilder extends Builder
{
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
        if( empty($name) ) {
            $connection=static::$connector->getDefaultConnection();
        }
        else {
            $connection=static::$connector->switchConnection($name);
        }
        $this->setConnection($connection);
        return $this;
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
        if( in_array($this->scenario, ['select']) )
        {
            $this->selects=['count(*)'];
            list($sql, $params)=$this->assemble();
            $stm=static::$connection->prepare($sql);
            $stm->execute($params);
            $count=current($stm->fetch());
            return $count;
        }
        else
        {
            throw new \Exception("count only support select.");
        }
    }

    public function max($column)
    {
        if( in_array($this->scenario, ['select']) )
        {
            $this->selects=["max($column)"];
            list($sql, $params)=$this->assemble();
            $stm=static::$connection->prepare($sql);
            $stm->execute($params);
            $count=current($stm->fetch());
            return $count;
        }
        else
        {
            throw new \Exception("max only support select.");
        }
    }

    public function min($column)
    {
        if( in_array($this->scenario, ['select']) )
        {
            $this->selects=["min($column)"];
            list($sql, $params)=$this->assemble();
            $stm=static::$connection->prepare($sql);
            $stm->execute($params);
            $count=current($stm->fetch());
            return $count;
        }
        else
        {
            throw new \Exception("min only support select.");
        }
    }

    public function sum($column)
    {
        if( in_array($this->scenario, ['select']) )
        {
            $this->selects=["sum($column)"];
            list($sql, $params)=$this->assemble();
            $stm=static::$connection->prepare($sql);
            $stm->execute($params);
            $count=current($stm->fetch());
            return $count;
        }
        else
        {
            throw new \Exception("sum only support select.");
        }
    }

    public function lastInsertId()
    {
        return static::$connection->lastInsertId();
    }

    /**
     *  trigger
     */
    public function execute()
    {
        if( in_array($this->scenario, ['insert', 'update', 'delete']) )
        {
			/**@var \PDO $pdo*/
			$pdo=static::getConnection();
            list($sql, $bindings)=$this->assemble();
            $sth=$pdo->prepare($sql);
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
        return $this->fetchRow();
    }

    public function fetchRow()
    {
        if( in_array($this->scenario, ['select']) )
        {
			/** @var  \PDO $pdo*/
			$pdo=static::getConnection();
            list($sql, $params)=$this->assemble();
            $stm=$pdo->prepare($sql);
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

    public function fetchPair()
    {

    }

    public function fetchGroup()
    {

    }

    public function fetchAll()
    {
        if( in_array($this->scenario, ['select']) )
        {
			/** @var  \PDO $pdo*/
			$pdo=static::getConnection();
            list($sql, $params)=$this->assemble();
            $stm=$pdo->prepare($sql);
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
		/** @var  \PDO $pdo **/
		$pdo=static::getConnection();
        return $pdo->setAttribute($key, $value);
    }

    public static function beginTransaction()
    {
		/** @var  \PDO $pdo **/
		$pdo=static::getConnection();
        if( !static::$transactionCounter++ )
            return $pdo->beginTransaction();
        return static::$transactionCounter >= 0;
    }

    public static function commit()
    {
		/** @var  \PDO $pdo **/
		$pdo=static::getConnection();
        if( !--static::$transactionCounter )
            return $pdo->commit();
        return static::$transactionCounter>=0;
    }

    public static function rollBack()
    {
        if( static::$transactionCounter>=0 ) {
            static::$transactionCounter=0;
			/** @var  \PDO $pdo **/
			$pdo=static::getConnection();
            return $pdo->rollBack();
        }
        static::$transactionCounter=0;
        return false;
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

        list($whereExp, $whereParams)=$this->where->assemble();
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
            $orders=array_map(function($item){
                return $item[0] . ' ' . $item[1];
            }, $this->orders);
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

        list($where, $params)=$this->where->assemble();
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
        list($where, $bindings)=$this->where->assemble();
        if( empty($where) ){
            throw new \Exception("where must not be null in delete.");
        }

        $sql="DELETE FROM {$this->table} WHERE $where";
        return array($sql, $bindings);
    }

	/**
	* @param Where $where
	 * @return array($expression, $bindings)
	*/
	protected function assembleWhere($where)
	{
		$bindings=[];
		$parts=[];
		foreach($where->getSubWhere() as $relation)
		{
			list($childExp, $childValues)=$this->assembleWhere($relation);
			$parts[]='(' . $childExp  . ')';
			$bindings=array_merge($bindings, $childValues);
		}

		$conditionsExp=[];
		foreach($where->getSubCondition() as $condition)
		{
			$conditionsExp[]=$condition->name;
			$bindings=array_merge($bindings, $condition->values);
		}

        $relation=str_pad($where->getRelationOperate(), 1, ' ', STR_PAD_BOTH);
		$parts[]=implode($relation , $conditionsExp);

		$parts=array_filter($parts, function($part){
			return !empty($part);
		});

		$expression=implode($relation, $parts);

		return array($expression, $bindings);
	}

}