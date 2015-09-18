<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-9-7
 * Time: 下午8:40
 */

namespace Scaffold\Filter;

use \Scaffold\Filter\Filter;

class CSRFFilter
{
    //TODO  session
    protected static $token;

    public function __construct()
    {

    }


    /**
    *  multi token
     * @return string
    */
    public static function getToken()
    {
        self::$token=sprintf("%x", rand(0x11111, 0xFFFFF));
        return self::$token;
    }

    /**
    * verify token
     * @return bool
    */
    public static function verifyToken()
    {
        return true;
    }



}

