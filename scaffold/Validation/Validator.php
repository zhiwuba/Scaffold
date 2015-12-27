<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scaffold\Validation;

use Scaffold\Exception\Exception;
use Scaffold\Helper\Utility;

class Validator
{
    /**
     * @var array
     */
    protected static $supportRules=[
        'active_url',
        'array',
        'between',
        'date',
        'email' ,
        'image',
        'in' ,
        'integer',
        'ip',
        'max',
        'min',
        'not_in' ,
        'regex',
        'required',
        'url'
    ];

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var array
     */
    protected $input;

    /**
     * @var string
     */
    protected $message;

    /**
     * Validator constructor.
     * @param $input
     * @param $rules
     */
    public function __construct($input, $rules)
    {
        $this->input=$input;
        $this->rules=$rules;
    }

    /**
     * create validator
     * eg: make($_GET, ['name'=>'required', 'age'=>'required|between:18,45'])
     *
     * @param array $input
     * @param array $rules
     * @return Validator
     */
    public static function make(array $input ,array $rules)
    {
        return new Validator($input, $rules);
    }

    /**
     *  trigger function
     *
     * @return bool
     * @throws Exception
     */
    public function fails()
    {
        foreach($this->rules as $key=>$ruleString)
        {
            $rules=explode('|', $ruleString);
            foreach($rules as $rule)
            {
                $parts=explode(':', $rule);
                if( count($parts)==1 ) {
                    $function=Utility::camelCase('is_' . $parts[0]);
                    $params=[isset($this->input[$key])? $this->input[$key] : ''];
                }
                else if( count($parts)==2 ) {
                    $function=Utility::camelCase('is_' . $parts[0]);
                    $params=[isset($this->input[$key])? $this->input[$key] : ''];
                    array_merge($params, explode(',', $parts[1]));
                }
                else {
                    throw new Exception("wrong rule  $rule");
                }

                if( is_callable([$this, $function]) ) {
                    $ret=call_user_func_array([$this,$function], $params);
                } else {
                    throw new Exception("unsupported rule {$parts[0]}");
                }

                if( $ret===false ) {
                    $this->message="$key isn't match $rule";
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function messages()
    {
        return $this->message;
    }

    protected function isActiveUrl($value)
    {
        $components=parse_url($value);
        if( $components!==false )
            return checkdnsrr($components['host']);
        else
            return false;
    }

    protected function isArray($value)
    {
        return is_array($value);
    }

    protected function isBetween($value, $min, $max)
    {
        return $value>=$min && $value<=$max;
    }

    protected function isDate($value)
    {
        return preg_match('#^\d{4}-\d{1,2}-\d{1,2}$#', $value);
    }

    protected function isEmail($value)
    {
        return preg_match('#^\w+@\w+\.\w+$#', $value);
    }

    protected function isImage($value)
    {
        return preg_match('#^\w+\.(jpg|png|bmp|gif)$#', $value );
    }

    protected function isIn()
    {
        $args=func_get_args();
        $value=array_shift($args);
        return in_array($value, $args);
    }

    protected function isInteger($value)
    {
        return is_integer($value);
    }

    protected function isIp($value)
    {
        return preg_match('#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $value);
    }

    protected function isMax($value, $len)
    {
        return strlen($value)<=$len;
    }

    protected function isMin($value, $len)
    {
        return strlen($value)>=$len;
    }

    protected function isNotIn()
    {
        $args=func_get_args();
        $value=array_shift($args);
        return !in_array($value, $args);
    }

    protected function isPassword($value)
    {
        return preg_match('#^\w+[6,10]$',$value);
    }

    protected function isRequire($value)
    {
        return !empty($value);
    }

    protected function isRegex($value,$regex)
    {
        return preg_match($regex, $value);
    }
}
