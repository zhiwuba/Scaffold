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
    *  @var  Array  all connector.
    * [
    *   'name'=>['host'=>'' , 'type'=>'read', 'connection'=>$connection] ,
    *   'name'=>['host'=>'', 'type'=>'write', 'connection'=>$connection]
    * ]
    */
    protected $connectors=[];


    /**
    * @var array
    */
    protected $config;


    /**
    * @param $configs Array.
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
            $this->connectors[$name]=[
                'host'=>$host,
                'type'=>'read',
                'connection'=>NULL
            ];
        }
        foreach($writes as $name=>$host)
        {
            $this->connectors[$name]=[
                'host'=>$host,
                'type'=>'write',
                'connection'=>NULL
            ];
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

    public function getDefaultConnection()
    {
        if( count($this->connectors)>0 )
        {
            $connector=&current($this->connectors);
            if( isset($connector['connection']) && $connector['connection'] instanceof \PDO )
            {
                return $connector['connection'];
            }
            else
            {
                $config=array_merge($this->config, ['host'=>$connector['host']]);
                $connector['connection']=$this->connect($config);
                return $connector['connection'];
            }
        }
        else
        {
            return NULL;
        }
    }

    /**
    *  switch connection.
    * @param $name string
    * @return \PDO
    */
    public function switchConnection($name)
    {
        foreach($this->connectors as &$connector)
        {
            if( $connector['name']===$name )
            {
                if( !isset($connector['connection']) || !($connector['connection'] instanceof \PDO) )
                {
                    $config=array_merge($this->config, ['host'=>$connector['host']]);
                    $connector['connection']=$this->connect($config);
                }
                return $connector['connection'];
            }
        }
        return null;
    }

    /**
     *  create connection
     * @param $config Array
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