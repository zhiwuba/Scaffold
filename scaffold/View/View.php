<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午3:06
 */

namespace Scaffold\View;

use Scaffold\Application\Application;
use Scaffold\Http\Stream;


class View
{
    /**
    *  @var \Scaffold\Application\Application $app
    */
    protected $app;

    /**
    *  @var  Array
    */
    protected $data=[];


    public function getApplication()
    {
        return $this->app;
    }

    public function setApplication(Application $app)
    {
        $this->app=$app;
    }


    /**
    *  append data
    */
    public function appendData($data)
    {
        array_merge($this->data, $data);
    }

    /**
    *   render template.
     * @param string $template
     * @param Array  $data
    */
    public function render($template, array $data)
    {
        $this->appendData($data);

        ob_start();
        extract($this->data);
        require $template;
        $content=ob_get_clean();

        $stream= Stream::createFromMemory();
        $stream->write($content);

        $this->app->response->withBody($stream);
    }
}
