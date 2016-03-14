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

    public function __construct()
    {
        $handler=new SessionHandler();
        session_set_save_handler($handler, true);
        session_name('session_id');
        session_start();
        $this->session=$_SESSION;
    }

    public static function setAdapter($adapter)
    {
        SessionHandler::setAdapter($adapter);
    }

    public static function getAdapter()
    {
        return SessionHandler::getAdapter();
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

    public function has()
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


