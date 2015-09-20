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
    * whether array is normal or not.
     * ['a', 'b', 'c', 'd']
    * @param Array $array
    * @return bool
    */
    public static function isNormalArray(&$array)
    {
        return is_array($array) && (array_keys($array)===range(0, count($array)- 1));
    }


    /**
    * whether array is assoc or not.
     * @param Array $array
     * @return bool
     */
    public static function isAssocArray(&$array)
    {
        return is_array($array) && array_diff_key($array, array_keys(array_keys($array)));
    }




}


