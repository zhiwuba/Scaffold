<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午3:06
 */


class Validator
{
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



}
