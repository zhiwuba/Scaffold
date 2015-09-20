<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午8:57
 */

namespace Sacaffold\Database\Query;

use Scaffold\Database\Query\Query;

class MysqlQuery extends Query
{
    protected $join=[];

    /**
     *  Cascade operate
     */
    public function join($table, $where)
    {
        $this->join[]="JOIN $table ON $where";
        return $this;
    }

    public function leftJoin($table, $where)
    {
        $this->join[]="LEFT JOIN $table ON $where";
        return $this;
    }

    public function rightJoin($table, $where)
    {
        $this->join[]="RIGHT JOIN $table ON $where";
        return $this;
    }

    public function fullJoin($table, $where)
    {
        $this->join[]="FULL JOIN $table ON $where";
        return $this;
    }

    public function union($table, $where)
    {

    }

    /**
     *  aggregation
     */
    public function count()
    {

    }

    public function max()
    {

    }

    public function min()
    {

    }

    public function sum()
    {

    }

    /**
     *  trigger
     */
    public function execute()
    {
        if( in_array($this->scenario, ['insert', 'update', 'delete']) )
        {

        }
        else
        {
            throw new \Exception("execute only support delete、update and insert!");
        }
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
    }

    protected function assembleSelect()
    {
        $keys='';
        $values='';
        $sql='';
        return array($sql, $values);
    }

    protected function assembleInsert()
    {
        $keys=implode(',', array_keys($this->data));
        $values=array_values($this->data);
        $placeholder=implode(',', array_fill(0, count($values), '?'));
        $sql="INSERT INTO {$this->table} ($keys) VALUES($placeholder)";
        return array($sql, $values);
    }

    protected function assembleUpdate()
    {
        $pair='';
        $values=array_values($this->data);
        $where='';
        $sql="UPDATE {$this->table} SET $pair WHERE $where";
        return array($sql, $values);
    }

    protected function assembleDelete()
    {
        $pair='';
        $values='';
        $where='';
        $sql="DELETE FROM {$this->table} WHERE $where";
        return array($sql, $values);
    }



    /**
    *  transaction.
    */
    public function transaction(\Closure $callback)
    {

    }

    public function beginTransaction()
    {

    }

    public function commit()
    {

    }

    public function rollBack()
    {

    }


}