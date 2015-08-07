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

$app=new Scaffold\Application\Application();

$app->run();
