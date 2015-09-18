<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-9-17
 * Time: 下午4:04
 */

namespace Scaffold\Filter;

abstract class Filter
{
    abstract public function load();

    abstract public function finish();
}


