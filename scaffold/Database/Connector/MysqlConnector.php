<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scaffold\Database\Connector;


class MysqlConnector extends Connector
{
    /**
    *  @var array
    * [
    *   'name'=>['host'=>'' , 'type'=>'read', 'connection'=>$connection] ,
    *   'name'=>['host'=>'', 'type'=>'write', 'connection'=>$connection]
    * ]
    */
    protected $connections=[];


    /**
	 * global config.
    * @var array
    */
    protected $config;


    /**
    * @param $configs array.
    */
    public function __construct($configs)
    {
        $this->config=array_filter($configs, function($var){
            return !is_array($var);
        });
        $reads=isset($configs['read'])? $configs['read'] : [];
        $writes=isset($configs['write'])? $configs['write'] : [];
        foreach($reads as $name=>$host)
        {
			$connection=new \stdClass();
			$connection->host=$host;
			$connection->type='read';
			$connection->connection=NULL;
            $this->connections[$name]=$connection;
        }
        foreach($writes as $name=>$host)
        {
			$connection=new \stdClass();
			$connection->host=$host;
			$connection->type='write';
			$connection->connection=NULL;
            $this->connections[$name]=$connection;
        }
    }

    public function getReadConnection()
    {
        return ;
    }

    public function getWriteConnection()
    {
        return ;
    }

    public function getConnection($name='')
    {
		$connection=empty($name)? current($this->connections) : $this->connections[$name];
		if( isset($connection->connection) && $connection->connection instanceof \PDO )
		{
			return $connection->connection;
		}
		else
		{
			$config=array_merge($this->config, ['host'=>$connection->host]);
			$connection->connection=$this->connect($config);
			return $connection->connection;
		}
	}

    /**
     *  create connection
     * @param $config array
     * @return \PDO
     */
    public function connect($config)
    {
        try
        {
            $dsn="{$config['driver']}:dbname={$config['database']};host={$config['host']}";
            $pdo=new \PDO($dsn , $config['username'], $config['password']);
            $pdo->query("set names={$config['charset']}");
            return $pdo;
        }
        catch(\PDOException $e)
        {
            die($e->getMessage());
        }
    }
}