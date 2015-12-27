<?php

require_once '../vendor/autoload.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

//$redisConnection=new \Predis\Client();

//\Scaffold\Cache\CacheItemPool::setAdapter(new \Scaffold\Cache\Adapter\RedisAdapter($redisConnection));


$app=new \Scaffold\Application\Application(dirname(__DIR__));

//$app->initRedisDB(APP_PATH . '/Configs/redis.php');
//$app->initMysqlDB(APP_PATH . '/Configs/mysql.php');
//$app->initElasticSearch(APP_PATH . '/Configs/elasticsearch.php');

$app->sourceRouteFile('../app/route.php');

$app->run();
