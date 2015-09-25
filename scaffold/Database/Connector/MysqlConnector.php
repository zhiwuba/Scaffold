<?php
/**
 * User: liubingxia
 * Date: 15-9-25
 * Time: 上午11:49
 */

namespace Scaffold\Database\Connector;


class MysqlConnector extends Connector
{
    /**
    *  @var  Array  all connector.
    * [
    *   'name'=>['type'=>'read', 'connection'=>$connection, 'config'=>[]] ,
    *   'name'=>['type'=>'write', 'connection'=>$connection, 'config'=>[]]
    * ]
    */
    protected $connectors;

    /**
    *  @var \PDO
    */
    protected $connector;

    /**
    *  @var int.  nesting transaction.
    */
    protected $transactionCounter=0;


    public static function loadConfig($configs)
    {

    }

    /**
    *  create connection
    */
    public static function connect($config)
    {
        try
        {
            $dsn="{$config['driver']}:database={$config['database']};host={$config['host']}";
            $pdo=new \PDO($dsn , $config['username'], $config['password']);
            $pdo->query("set names={$config['charset']}");
            return $pdo;
        }
        catch(\PDOException $e)
        {
            die($e->getMessage());
        }
    }



    public function getReadConnector()
    {

        return ;
    }

    public function getWriteConnector()
    {

        return ;
    }

    public function getConnectorByName($name)
    {
        foreach($this->connectors as &$connector)
        {
            if( $connector['name']===$name )
            {
                if( !isset($connector['connection']) || !($connector['connection'] instanceof \PDO) )
                {
                    $connector['connection']=$this->connect($connector['config']);
                }
                return $connector['connection'];
            }
        }
        return null;
    }

    /**
     *  transaction.
     */
    public function transaction(\Closure $callback)
    {
        try
        {
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->beginTransaction();
            $callback();
            $this->commit();
            return ['ret'=>1];
        }
        catch(\PDOException $e)
        {
            $this->rollBack();
            return ['ret'=>0, 'error'=>$e->getMessage()];
        }
    }


    public function setAttribute($key, $value)
    {
        return $this->connector->setAttribute($key, $value);
    }

    public function beginTransaction()
    {
        if( !$this->transactionCounter++ )
            return $this->connector->beginTransaction();
        return $this->transactionCounter >= 0;
    }

    public function commit()
    {
        if( !--$this->transactionCounter )
            return $this->connector->commit();
        return $this->transactionCounter>=0;
    }

    public function rollBack()
    {
        if( $this->transactionCounter>=0 )
        {
            $this->transactionCounter=0;
            return $this->connector->rollBack();
        }
        $this->transactionCounter=0;
        return false;
    }
}