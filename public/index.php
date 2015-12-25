<?php

require_once '../vendor/autoload.php';

//$redisConnection=new \Predis\Client();

//\Scaffold\Cache\CacheItemPool::setAdapter(new \Scaffold\Cache\Adapter\RedisAdapter($redisConnection));


$app=new \Scaffold\Application\Application('');

$app->initRedisDB(APP_PATH . '/Configs/redis.php');
$app->initMysqlDB(APP_PATH . '/Configs/mysql.php');
$app->initElasticSearch(APP_PATH . '/Configs/elasticsearch.php');

$app->sourceRouteFile(APP_PATH . '/route.php');

$app->run();
