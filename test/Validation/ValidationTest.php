<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Validation;


use Scaffold\Validation\Validator;
use Test\TestCase;

class ValidationTest extends TestCase
{
    public function testValidation()
    {
        $validation=Validator::make($_GET, [
            'name'=>'string',
            'password'=>'password',
            'email'=>'email',
            'url'=> 'url',
            'region'=>'in:china,japan',
            'age'=>'between:18,45',
            'avatar'=>'image',
            'login'=>'ip',
            'birthday'=>'date',
        ]);
        if( $validation->fails() )
        {

        }
    }
}


