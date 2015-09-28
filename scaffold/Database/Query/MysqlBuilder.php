<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午8:57
 */

namespace Scaffold\Database\Query;

use Scaffold\Database\Query\Builder;

class MysqlBuilder extends Builder
{
    /**
    *  @var \PDO will be array for distribute arch.
    */
    public static $adapter;

    /**
     * @var array
     */
    protected $joins=[];

    /**
    *  @var string . TODO array
    */
    protected $haves;

    public static function getMysqlAdapter()
    {
        return static::$adapter;
    }

    public static function setMysqlAdapter($adapter)
    {
        static::$adapter=$adapter;
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
            $stm=static::$adapter->prepare($sql);
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
            $stm=static::$adapter->prepare($sql);
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
            $stm=static::$adapter->prepare($sql);
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
            $stm=static::$adapter->prepare($sql);
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
        return static::$adapter->lastInsertId();
    }

    /**
     *  trigger
     */
    public function execute()
    {
        if( in_array($this->scenario, ['insert', 'update', 'delete']) )
        {
            list($sql, $bindings)=$this->assemble();
            $sth=static::$adapter->prepare($sql);
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
            list($sql, $params)=$this->assemble();
            $stm=static::$adapter->prepare($sql);
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
            list($sql, $params)=$this->assemble();
            $stm=static::$adapter->prepare($sql);
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

}