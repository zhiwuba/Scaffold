<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-26
 * Time: 下午10:50
 */

namespace Test\Http;

use Scaffold\Http\Uri;
use Test\TestCase;

class ServerRequestTest extends TestCase
{
    /**
     * @before
     */
    public function setEnv()
    {
        $server=<<<EOF
        {
    "USER": "www-data",
    "HOME": "\/var\/www",
    "FCGI_ROLE": "RESPONDER",
    "SCRIPT_FILENAME": "\/home\/liubingxia\/workspace\/Scaffold\/public\/test.php",
    "QUERY_STRING": "name=lbx&sort=desc",
    "REQUEST_METHOD": "GET",
    "CONTENT_TYPE": "",
    "CONTENT_LENGTH": "",
    "SCRIPT_NAME": "\/test.php",
    "REQUEST_URI": "\/test.php?name=lbx&sort=desc",
    "DOCUMENT_URI": "\/test.php",
    "DOCUMENT_ROOT": "\/home\/liubingxia\/workspace\/Scaffold\/public",
    "SERVER_PROTOCOL": "HTTP\/1.1",
    "GATEWAY_INTERFACE": "CGI\/1.1",
    "SERVER_SOFTWARE": "nginx\/1.6.2",
    "REMOTE_ADDR": "127.0.0.1",
    "REMOTE_PORT": "37051",
    "SERVER_ADDR": "127.0.0.1",
    "SERVER_PORT": "80",
    "SERVER_NAME": "scaffold.com",
    "REDIRECT_STATUS": "200",
    "HTTP_HOST": "scaffold.com",
    "HTTP_USER_AGENT": "Mozilla\/5.0 (X11; Ubuntu; Linux x86_64; rv:42.0) Gecko\/20100101 Firefox\/42.0",
    "HTTP_ACCEPT": "text\/html,application\/xhtml+xml,application\/xml;q=0.9,*\/*;q=0.8",
    "HTTP_ACCEPT_LANGUAGE": "zh-CN,en-US;q=0.7,en;q=0.3",
    "HTTP_ACCEPT_ENCODING": "gzip, deflate",
    "HTTP_CONNECTION": "keep-alive",
    "HTTP_CACHE_CONTROL": "max-age=0",
    "PHP_SELF": "\/test.php",
    "REQUEST_TIME_FLOAT": 1451028380.0803,
    "REQUEST_TIME": 1451028380
}
EOF;
        $_SERVER=json_decode($server, true);

        $_GET=['name'=>'lbx', 'sort'=>'desc'];

        $_POST=[];

        $_SESSION=[];
    }


    public function testUri()
    {
        $uri=Uri::createFromEnv();

        $authority=$uri->getAuthority();
        $this->assertEquals('scaffold.com:80', $authority);

        $fragment=$uri->getFragment();
        $this->assertEquals('', $fragment);

        $host=$uri->getHost();
        $this->assertEquals('scaffold.com', $host);

        $path=$uri->getPath();
        $this->assertEquals('test.php', $path);

        $port=$uri->getPort();
        $this->assertEquals(80, $port);

        $query=$uri->getQuery();
        $this->assertEquals('name=lbx&sort=desc', $query);

        $scheme=$uri->getScheme();
        $this->assertEquals('http', $scheme);

        $userInfo=$uri->getUserInfo();
        $this->assertEquals('', $userInfo);
    }

    public function testStream()
    {

    }

    public function testRequest()
    {

    }

    public function testResponse()
    {

    }

    public function testCookie()
    {

    }

    public function testServerRequest()
    {

    }

}

