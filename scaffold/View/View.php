<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午3:06
 */

namespace Scaffold\View;

use Scaffold\Http;

class View
{
    /**
    *  @var \Scaffold\Application\Application $app
    */
    protected $app;


    protected $data;

    /**
    *  append data
    */
    public function appendData($data)
    {
        array_merge($this->data, $data);
    }

    /**
    *   render template.
    */
    public function render($template, array $data)
    {
        $this->appendData($data);

        ob_start();
        extract($this->data);  //overwrite.
        require $template;
        $content=ob_get_clean();

        $stream=new Http\Stream();
        $stream->write($content);

        $this->app->response->withBody($stream);
    }
}
