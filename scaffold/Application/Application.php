<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午2:14
 */

namespace Scaffold\Application;

use Scaffold\Cache\CacheItemPool;
use Scaffold\Database\Connector\CassandraConnector;
use Scaffold\Database\Connector\ElasticSearchConnector;
use Scaffold\Database\Model\ModelException;
use Scaffold\Database\Query\CassandraBuilder;
use Scaffold\Database\Query\ElasticSearchBuilder;
use Scaffold\Database\Query\MysqlBuilder;
use Scaffold\Database\Connector\MysqlConnector;
use Scaffold\Helper\Container;
use Scaffold\Http\Response;
use Scaffold\Http\ServerRequest;
use Scaffold\Log\Logger;
use Scaffold\Routing\Router;
use Scaffold\Session\Session;
use Scaffold\View\View;


/**
 *  Application
 * @property \Scaffold\Routing\Router $router
*  @property  \Scaffold\Http\ServerRequest $request
 * @property  \Scaffold\Http\Response $response
 * @property  \Scaffold\Log\Logger    $logger
 */
class Application
{
    /**
    *  @var Container
    */
    protected static $container;

    /**
     * @var Application
     */
    protected static $instance;

    protected $rootPath;

    public static function getInstance($name='')
    {
        if( empty($name) )
            return static::$instance;
        else
            return static::$container->get($name);
    }

    public function __construct($rootPath)
    {
        static::$instance=$this;

        static::$container=new Container();

        $this->rootPath=$rootPath;

        $this->container->singleton('Request', function(){
            return new ServerRequest();
        });

        $this->container->singleton('Response', function(){
           return new Response();
        });

        $this->container->singleton('Logger', function(){
            $logFile=ROOT_PATH . '/log/default.log';
            $logger=Logger::createFileLogger($logFile);
            return $logger;
        });

        $this->container->singleton('Router', function(){
            return new Router($this);
        });

        $this->container->singleton('Session', function(){
            return new Session();
        });

        $this->container->singleton('View', function(){
            return new View();
        });

        $this->container->singleton('Cache', function(){
            return new CacheItemPool();
        });

        static::$instance=$this;
    }

    public function __get($name)
    {
        return $this->container->get($name);
    }

    public function __set($name, $value)
    {
        $this->container->set($name, $value);
    }

    public function __isset($name)
    {
        return $this->container->has($name);
    }

    public function __unset($name)
    {
        $this->container->remove($name);
    }

    /**
    *  include route file.
     * @param  string $filePath
    */
    public function sourceRouteFile($filePath)
    {
        require_once "$filePath";
    }

    /**
    * source mysql config.
    */
    public function initMysqlDB($filePath)
    {
        $config=require_once $filePath;
        $connector=MysqlConnector::loadConfig($config);
        MysqlBuilder::setConnector($connector);
    }

	public function initElasticSearch($filePath)
	{
		$config=require_once $filePath;
		$connector=ElasticSearchConnector::loadConfig($config);
		ElasticSearchBuilder::setConnection($connector->getConnection());
	}

	public function initCassandraDB($filePath)
	{
		$config=require_once $filePath;
		$connector=CassandraConnector::loadConfig($config);
		CassandraBuilder::setConnection($connector->getConnection());
	}

	public function initRedisDB($filePath)
	{
		$config=require_once $filePath;
	}

	public function initMongoDB($filePath)
	{
		$config=require_once $filePath;
	}

    public function initRabbitMQ($filePath)
    {
        $config=require_once $filePath;
    }

    /**
    *  dispatch request by router
    */
    public function dispatch()
    {
        if( $this->request!==null )
        {
            $this->router->dispatch($this->request );
        }
    }

    /**
    *  send header
    */
    public function sendHeader()
    {
        if( headers_sent() === false ){
            $headers=$this->response->getHeaders();
            foreach($headers as $name=>$values){
                foreach($values as $value){
                    header("$name: $value", false);
                }
            }
        }
    }

    /**
    *  send body
    */
    public function sendBody()
    {
        echo $this->response->getBody();
    }

	/**
	 * render exception to front.
	 * @param $e \Exception
	 */
	public function renderException($e)
	{
		echo $e->getMessage() . '<br>';
		echo $e->getLine() . '<br>';
		print_r($e->getTrace());
	}

    /**
    *  run , active all process.
    */
    public function run()
    {
        try
        {
            $this->dispatch();
            $this->sendHeader();
            $this->sendBody();

            if (function_exists("fastcgi_finish_request")) {
                fastcgi_finish_request();
            }
        }
		catch(ModelException $e)
		{

		}
        catch(\Exception $e)
        {
			$this->logger->error($e->getMessage());
			$this->renderException($e);
            exit;
        }
    }
}
