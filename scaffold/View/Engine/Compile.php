<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scaffold\View\Engine;


class Compile
{
    /**
     * @var  [['name'=>'value']]
     */
    protected $sections;

    /**
     * @var \SplStack
     */
    protected $stacks;

    /**
     *  view cache path
     * @var  string
     */
    protected $cachePath;


    public function __construct($cachePath)
    {
        $this->cachePath=$cachePath;
        $this->stacks=new \SplStack();
    }

    /**
     * compile files
     * @param $filename string
     * @param $params  []
     * @return string
     */
    public function compile($filename, $params=[])
    {
        $cacheFilename=$this->getParsedFile($filename);
        return $this->build($cacheFilename, $params);
    }

    public function getSections()
    {
        return $this->sections;
    }

    public function startSection($name)
    {
        $this->stacks[]=$name;
        ob_start();
    }

    public function showSection()
    {
        $name=$this->stacks->pop();
        $content=ob_get_clean();
        if( isset($this->sections[$name]) )
        {
            $this->sections[$name]=preg_replace(['#@@parent#', '#@parent#'] , [$content, $content], $this->sections[$name] );
            echo $this->sections[$name];
        }
    }

    public function stopSection()
    {
        $name=$this->stacks->pop();
        $content=ob_get_clean();
        $this->sections[$name]=$content;
    }

    public function appendSection()
    {
        $name=$this->stacks->pop();
        $content=ob_get_clean();
        if( isset($this->sections[$name]) )
            $this->sections[$name] .=$content;
        else
            $this->sections[$name]=$content;
    }

    public function overwriteSection()
    {
        $name=$this->stacks->pop();
        $content=ob_get_clean();
        $this->sections[$name]=$content;
    }

    public function extendsTemplate($path)
    {
        echo $this->compile($path);
    }

    public function includeTemplate($path)
    {
        echo $this->compile($path);
    }

    protected function getParsedFile($filename)
    {
        $cacheFilename=$this->cachePath . md5($filename);

        //  check  whether the cache file is expired or not.
        if( !file_exists($cacheFilename) || filemtime($cacheFilename)<filemtime($filename) )
        {
            $content=Parse::parse($filename);

            $file=fopen($cacheFilename, 'w');
            fwrite($file, $content ,strlen($content));
            fclose($file);
        }

        return $cacheFilename;
    }

    protected function build($filename, $params)
    {
        if( !isset($params['__compile']) )
        {
            $params['__compile']=$this;
        }

        ob_start();
        extract($params);
        require "$filename";
        return ob_get_clean();
    }

}
