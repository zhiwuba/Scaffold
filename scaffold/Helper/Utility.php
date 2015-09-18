<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-27
 * Time: 上午10:41
 */

namespace Scaffold\Helper;

class Utility
{

    /**
    * whether array is normal(assoc)  or not.
    * @param Array $array
    * @return bool
    */
    public static function isNormalArray(&$array)
    {
        return array_keys($array)===range(0, count($array)- 1);
    }


    /**
     * whether array is flat or not.
    * @param Array $array
     * @return bool
     */
    public static function isFlatArray(&$array)
    {
        foreach( $array as $key=>$value )
        {
            if( is_array($value) ){
                return false;
            }
        }
        return true;
    }

}


