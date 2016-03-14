<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Session;

use Scaffold\Session\Session;
use Test\TestCase;

class SessionTest extends TestCase
{
    public function testSession()
    {
        $session=new Session();
        $_SESSION['name']='lbx';

    }
}

