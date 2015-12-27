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
    * @param array $array
    * @return bool
    */
    public static function isNormalArray(&$array)
    {
        return is_array($array) && (array_keys($array)===range(0, count($array)- 1));
    }


    /**
    * whether array is assoc or not.
     * @param array $array
     * @return bool
     */
    public static function isAssocArray(&$array)
    {
        return is_array($array) && array_diff_key($array, array_keys(array_keys($array)));
    }

    /**
    *  flatten array
     * @param array $array
     * @return array
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
    *  @param array $array
     * @return array
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
     * @param array $array1
     * @param array $array2
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

	/**
	 * get cost time of function
	 */
	public static function functionCostTime($callback)
	{
		$tm1=microtime(true)*1000;
		$return=$callback();
		$cost=(int)(microtime(true)*1000 - $tm1);
		return array($cost, $return);
	}

    /**
     *  get the second interval of two datetime object.
     *
     * @param \DateTime $time1
     * @param \DateTime $time2
     * @return int
     */
    public static function dateTimeInterval(\DateTime $time1, \DateTime $time2)
    {
        $interval=$time1->diff($time2);
        return Utility::dateIntervalToSec($interval);
    }

    public static function dateIntervalToSec(\DateInterval $interval)
    {
        return $interval->days*86400+$interval->h*3600 +$interval->m*60+$interval->s;
    }

    /**
     *  generate global id
     *
     * @return integer (64)
     */
    public static function generateId()
    {
        $id=microtime(true); //todo
        return $id;
    }

}


