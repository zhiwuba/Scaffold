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

    public function fails()
    {
        return $this->result;
    }

    public function messages()
    {
        return $this->message;
    }

    protected function isNumber()
    {

    }

    public function isString()
    {

    }

    public function isEmail()
    {

    }

    public function isPassword()
    {

    }

    public function isRequire()
    {
        $args=func_get_args();
    }

    protected function filter(array $input,  array $rules)
    {
        foreach($rules as $key=>$rule )
        {
            $ruleItems=explode('|', $rule);

        }
    }

    protected function match($input, $key, $rule)
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
