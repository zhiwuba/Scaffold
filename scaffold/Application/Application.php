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
 *   Application core
 *
 * @property  \Scaffold\Routing\Router          $router
*  @property  \Scaffold\Http\ServerRequest    $request
 * @property  \Scaffold\Http\Response           $response
 * @property  \Scaffold\Log\Logger                 $logger
 * @property  \Scaffold\Cache\CacheItemPool $cache
 * @property  \Scaffold\View\View                   $view
 * @property  \Scaffold\Session\Session            $session
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

    /**
     * @var string
     */
    protected $rootPath;

    protected $configPath;

    protected $logPath;

    /**
     * @param string $name
     * @return null|Application
     */
    public static function getInstance($name='')
    {
        if( empty($name) )
            return static::$instance;
        else
            return static::$container->get($name);
    }

    /**
     * Application constructor.
     * @param $rootPath
     */
    public function __construct($rootPath)
    {
        static::$instance=$this;

        static::$container=new Container();

        $this->rootPath=$rootPath;

        static::$container->singleton('request', function(){
            return new ServerRequest();
        });

        static::$container->singleton('response', function(){
           return new Response();
        });

        static::$container->singleton('logger', function(){
            $logFile=$this->rootPath . '/log/default.log';
            $logger=Logger::createFileLogger($logFile);
            return $logger;
        });

        static::$container->singleton('router', function(){
            return new Router($this);
        });

        static::$container->singleton('session', function(){
            return new Session();
        });

        static::$container->singleton('view', function(){
            return new View($this);
        });

        static::$container->singleton('cache', function(){
            return new CacheItemPool();
        });

        static::$instance=$this;
    }

    public function getViewPath()
    {
        return $this->rootPath . '/app/Views/';
    }

    public function getViewCachePath()
    {
        return $this->rootPath . '/storage/views/';
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
    *  send http header
    */
    public function sendHeader()
    {
        if( headers_sent() === false ){
            $statusLine=$this->response->getStatusLine();
            header($statusLine, false);
            $headers=$this->response->getHeaders();
            foreach($headers as $name=>$values){
                foreach($values as $value){
                    header("$name: $value", false);  //todo
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
        catch(\Scaffold\Exception\Exception $e)
        {

        }
        catch(\Exception $e)
        {
			$this->logger->error($e->getMessage());
			$this->renderException($e);
            exit;
        }
    }

    public function __get($name){
        return static::$container->get($name);
    }
    public function __set($name, $value){
        static::$container->set($name, $value);
    }
    public function __isset($name){
        return static::$container->has($name);
    }
    public function __unset($name){
        static::$container->remove($name);
    }
}
