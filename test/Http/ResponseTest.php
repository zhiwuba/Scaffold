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


use Scaffold\Http\Response;
use Scaffold\Http\Stream;
use Test\TestCase;

class ResponseTest extends TestCase
{
    public function testResponse()
    {
        $response=new Response();
        $response->withStatus(404);
        $response->withHeader("Code", 'BYTE');
        $response->withAddedHeader('Array', "12");
        $response->withAddedHeader('Array', "13");

        $stream=Stream::createFromMemory();
        $stream->write("<h1>404</h1>");
        $response->withBody($stream);
        $response->getReasonPhrase();

        echo $response->getStatusLine(),"\r\n";
        $headers=$response->getHeaders();
        foreach($headers as $name=>$values){
            foreach($values as $value){
                echo "$name: $value\r\n";
            }
        }

        echo strval($response->getBody());
    }
}
