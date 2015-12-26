<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *  1. easy extension.
 *  todo
 */

namespace Scaffold\Validation;

use Scaffold\Exception\Exception;
use Scaffold\Helper\Utility;
use Scaffold\Http\Uri;

class Validator
{
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
    protected $rules;
    protected $input;
    protected $result;
    protected $message;

    public function __construct($input, $rules)
    {
        $this->input=$input;
        $this->rules=$rules;
    }

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

    public function messages()
    {
        return $this->message;
    }

    private function isActiveUrl($value)
    {
        $components=parse_url($value);
        if( $components!==false )
            return checkdnsrr($components['host']);
        else
            return false;
    }

    private function isArray($value)
    {
        return is_array($value);
    }

    private function isBetween($value, $min, $max)
    {
        return $value>=$min && $value<=$max;
    }

    private function isEmail($value)
    {
        return preg_match('#\w+@\w+\.\w+#', $value);
    }

    private function isImage()
    {
        return preg_match();
    }

    private function isIn()
    {
        $args=func_get_args();
        $value=array_shift($args);
        return in_array($value, $args);
    }

    private function isInteger($value)
    {
        return is_integer($value);
    }

    private function isPassword()
    {
        return true;
    }

    private function isRequire($value)
    {
        return !empty($value);
    }

    private function isString($value)
    {
        return is_string($value);
    }
}
