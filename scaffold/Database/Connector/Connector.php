<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-9-25
 * Time: 上午11:48
 */

namespace Scaffold\Database\Connector;

abstract class Connector
{
    /**
    *  load config file
     * @param $config array
    */
    abstract public function loadConfig($config);

}
