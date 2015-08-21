<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午3:04
 */

namespace Scaffold\Session;

class Session
{
    protected $session;
    protected $driver;

    public function __construct()
    {
        session_start();
        $this->session=$_SESSION;
    }

    public function setDriver()
    {

    }

    public function get()
    {

    }

    /**
    *   存储数据到session
     * eg: put('key', 'value');
    */
    public function put()
    {

    }

    /**
    *   存储数据到session的数组值中
     *  eg: push('user.teams', 'developers');
    */
    public function push()
    {

    }

    public function update()
    {

    }

    public function delete()
    {

    }

    /**
    *  所有数据
    */
    public function all()
    {

    }
}


