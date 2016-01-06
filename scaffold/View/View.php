<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-7
 * Time: 下午3:06
 */

namespace Scaffold\View;

use Scaffold\Application\Application;
use Scaffold\Application\AppTrait;
use Scaffold\Http\Stream;


class View
{
    use AppTrait;

    /**
    *  @var  array
    */
    protected $data=[];

    /**
     * View constructor.
     * @param $app
     */
    public function __construct($app)
    {
        $this->setApplication($app);
    }

    /**
    *  append data
     * @param $data array
    */
    public function appendData($data)
    {
        $this->data=array_merge($this->data, $data);
    }

    /**
    *   render template.
     * @param string $template
     * @param array  $data
    */
    public function render($template, array $data)
    {
        $this->appendData($data);

        ob_start();
        extract($this->data);
        $templatePath=$this->getTemplatePath($template);
        require "$templatePath";
        $content=ob_get_clean();

        $stream= Stream::createFromMemory();
        $stream->write($content);

        $this->app->response->withBody($stream);
    }

    /**
     * @param $name
     * @return string
     */
    public function getTemplatePath($name)
    {
        if( strpos('.view.php', $name) ) {
            return $this->app->getViewPath() . $name;
        }
        else {
            return $this->app->getViewPath() . $name . '.view.php';
        }
    }

}
