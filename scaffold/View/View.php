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
use Scaffold\View\Engine\Compile;


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

        $compile=new Compile($this->app->getViewCachePath());
        $content=$compile->compile($this->getTemplatePath($template), $this->data);

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
        if( strpos('.blade.php', $name) ) {
            return $this->app->getViewPath() . $name;
        }
        else {
            return $this->app->getViewPath() . $name . '.blade.php';
        }
    }

}
