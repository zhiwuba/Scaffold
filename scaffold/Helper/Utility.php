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

    /**
    *  flatten array
     * @param Array $array
     * @return Array
    */
    public static function arrayFlatten(&$array)
    {
        $result=[];
        foreach($array as $key=>$value)
        {
            if( is_array($value) )
            {
                $result=array_merge($result, Utility::arrayFlatten($value));
            }
            else
            {
                $result[]=$value;
            }
        }
        return $result;
    }

    /**
     * flatten an array and use dot to represent deep.
    *  @param Array $array
     * @return Array
    */
    public static function arrayDot(&$array, $prefix='')
    {
        $result=[];
        foreach( $array as $key=>$value )
        {
            if( is_array($value) )
            {
                if( is_string($key) )
                {
                    $tmpPrefix=empty($prefix) ? $key : ($prefix . '.' . $key);
                }
                else
                {
                    $tmpPrefix=$prefix;
                }
                $result=array_merge($result, Utility::arrayDot($value, $tmpPrefix));
            }
            else
            {
                $result[]=empty($prefix) ? $value : ($prefix . '.' . $value);
            }
        }
        return $result;
    }

    /**
    *  whether array1 is array2 's subset or not.
     * different from array_diff_assoc().
     * @param Array $array1
     * @param Array $array2
     * @return bool
    */
    public static function isSubSet($array1, $array2)
    {
        foreach( $array1 as $key=>$value ) {
            if( isset($array2[$key]) ) {
                if( is_array($value) ) {
                    if( !Utility::isSubSet($value, $array2[$key]) ) {
                        return false;
                    }
                }
                else {
                    if( $value!==$array2[$key] ) {
                        return false;
                    }
                }
            }
            else {
                return false;
            }
        }
        return true;
    }

    /**
    *  snake to camel.
    */
    public static function camelCase($input)
    {
        $words=explode('_', $input);
        $result=array_shift($words);
        foreach( $words as $word )
        {
            $result .=ucfirst($word);
        }
        return $result;
    }

    /**
    * camel to snake
    */
    public static function snakeCase($input)
    {
        $result=preg_replace_callback('#[A-Z]#', function($matches){
            return '_' . strtolower($matches[0]);
        }, $input );
        return $result;
    }

}


