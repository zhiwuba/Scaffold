<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Http;

use Scaffold\Http\Message;
use Test\TestCase;

class MessageTest extends TestCase
{
    public function testMessage()
    {
        $message=new Message();
        $message->withHeader('foo', 'bar')
            ->withAddedHeader('foo', 'baz');
        $header=$message->getHeaderLine('foo');
        $this->assertEquals('bar,baz', $header);

        $header=$message->getHeader('foo');
        $this->assertArraySubset(['bar', 'baz'], $header );
    }
}