<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scaffold\Application;

trait AppTrait
{
    /**
     * @var Application
     */
    public $app;

    final public function setApplication($app)
    {
        $this->app=$app;
    }

    final public function getApplication()
    {
        return $this->app;
    }

    /**
     *  提供一种语法糖来快捷调用类中的非静态方法
     *
     * @param $name string
     * @param $arguments array
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $object=Application::getInstance(get_called_class());
        return call_user_func_array([$object, $name], $arguments);
    }

}