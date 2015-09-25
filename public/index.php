<?php
/**
 * User: explorer
 * Date: 2015/8/5
 * Time: 23:39
 */


set_include_path(implode(PATH_SEPARATOR, [
    get_include_path(),
]));

require_once '../vendor/autoload.php';

define('ROOT_PATH', dirname(__DIR__));


$app=new \Scaffold\Application\Application();

$app->sourceMysqlFile(ROOT_PATH . 'app/Configs/mysql.php');

$app->sourceRouteFile(ROOT_PATH . '/app/route.php');

$app->run();
