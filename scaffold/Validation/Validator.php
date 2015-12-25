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

use Scaffold\Http\Uri;

class Validator
{
    protected static $supportRules=[
        'active_url'  =>['one',  ],
        'array'         =>['one',   ],
        'between'   =>['some', ],
        'date'          =>['none', ],
        'email'        =>['none', ],
        'image'       =>['none', ],
        'in'              =>['some', ],
        'integer'     =>['none', ],
        'ip'              =>['none', ],
        'max'          =>['one',   ],
        'min'           =>['one',   ],
        'not_in'       =>['some', ],
        'regex'        =>['one',   ],
        'required'  =>['none', ],
        'url'            =>['one',   ]
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
     * @return mixed
     */
    public function fails()
    {
        //
        return $this->result;
    }

    public function messages()
    {
        return $this->message;
    }

    private function isNumber()
    {

    }

    private function isString()
    {

    }

    private function isEmail()
    {

    }

    private function isPassword()
    {

    }

    private function isRequire($array, $key)
    {

    }

    private function isBetween($value, $min, $max)
    {
        return $value>=$min && $value<=$max;
    }

    private function isActiveUrl($url)
    {
        $components=parse_url($url);
        if( $components!==false )
            return checkdnsrr($components['host']);
        else
            return false;
    }

    private function isArray($args)
    {
        return is_array($args);
    }

    private function filter(array $input,  array $rules)
    {
        foreach($rules as $key=>$rule )
        {
            $ruleItems=explode('|', $rule);

        }
    }

    private function match($input, $key, $rule)
    {
        switch($rule)
        {
            case 'required':
                return isset($input[$key]);
            case 'number':
                break;
            case 'string':
                break;
            case 'array':
                break;
            case 'email':
                break;
            case 'password':
                break;
            case 'ip':
                break;
            case 'image':
                break;
        }
    }
}
