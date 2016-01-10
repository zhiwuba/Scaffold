<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace  Scaffold\View\Engine;
use Scaffold\Exception\ViewException;

/**
 * TemplateEngine Inspire By Blade.
 * @link  http://www.golaravel.com/laravel/docs/5.1/blade/
 * @package Scaffold\View\Engine
 */
class Parse
{
    /**
     *  template path.
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $parsedText;

    public function __construct($filename)
    {
        if( is_file($filename) )
        {
            $this->filename=$filename;
            $this->parsedText=$this->readFile($filename);
        }
        else
        {
            throw new ViewException("file $filename not exist.");
        }
    }

    /**
     *  compile
     *
     * @param  $filename string
     * @return string  parsedText
     */
    public static function parse($filename)
    {
        $parser=new static($filename);

        $ref=new \ReflectionClass(static::class);
        $methods=$ref->getMethods(\ReflectionMethod::IS_PROTECTED);
        foreach($methods as $method)
        {
            $name=$method->getName();
            if( preg_match('#^parse\w+$#', $name) )
            {
                call_user_func([$parser, $name]);
            }
        }

        /*
        $parser->parseInclude();
        $parser->parseYield();
        $parser->parseSection();
        $parser->parseEndsection();
        $parser->parseAppend();
        $parser->parseOverwrite();
        $parser->parseExtends();
        $parser->parseShow();
        $parser->parseStop();

        $parser->parseIf();
        $parser->parseElse();
        $parser->parseElseIf();
        $parser->parseEndIf();

        $parser->parseWhile();
        $parser->parseEndWhile();

        $parser->parseFor();
        $parser->parseEndfor();

        $parser->parseForeach();
        $parser->parseEndforeach();

        $parser->parseComment();
        $parser->parseRawEcho();
        $parser->parseEcho();
        */
        return $parser->getParsedText();
    }

    public function getParsedText()
    {
        return $this->parsedText;
    }


    protected function parseBlankLines()
    {
        $this->pregReplace('#[\n\r]{1,}#', "\n");
    }

    /**
     * parse @yield
     */
    protected function parseYield()
    {
        $this->pregReplace('#@yield\(\s*\'(\w+)\'\s*\)#', function($matches){
            return '<?php $__compile->startSection(\''. $matches[1] .'\'); ?><?php $__compile->showSection();?>';
        });
    }


    /**
     * compile @extends tag
     */
    protected function parseExtends()
    {
        $pathRegex=$this->getCommonRegex('path');
        $ret=preg_match('#@extends\(\s*' . $pathRegex . '\s*\)#', $this->parsedText, $matches);
        if($ret!=0 )
        {
            $this->pregReplace('#@extends\(\s*' . $pathRegex .'\s*\)#', '');
            $filename=$this->shortNameToFilename($matches[1]);
            $this->parsedText .=  ("\n" . '<?php $__compile->extendsTemplate(\''. $filename .'\');?>');
        }
    }

    /**
     * parse @include tag
     */
    protected function parseInclude()
    {
        $pathRegex=$this->getCommonRegex('path');
        $this->pregReplace('#@include\(\s*' . $pathRegex .'\s*\)#', function($matches){
            $filename=$this->shortNameToFilename($matches[1]);
            return '<?php $__compile->includeTemplate(\''. $filename .'\');?>';
        });
    }

    /**
     * compile @section tag
     */
    protected function parseSection()
    {
        $this->pregReplace( '#@section\(\s*\'(\w+)\'\s*\)#', function($matches){
            return '<?php $__compile->startSection(\'' . $matches[1] . '\');?>';
        });

        $this->pregReplace('#@section\(\s*\'(\w+)\'\s*,\s* \'([^\']*)\'\s*\)#', function($matches){
            return '<?php $__compile->startSection(\''. $matches[1] .'\'); ?>'. $matches[2] .'<?php $__compile->stopSection();?>';
        });
    }

    /**
     * parse @show tag
     */
    protected function parseShow()
    {
        $this->pregReplace( '#@show#', '<?php $__compile->showSection();?>');
    }

    /**
     * compile @stop tag
     */
    protected function parseStop()
    {
        $this->pregReplace('#@stop#', '<?php $__compile->stopSection();?>' );
    }

    /**
     * parse @append tag
     */
    protected function parseAppend()
    {
        $this->pregReplace('#@append#', '<?php $__compile->appendSection(); ?>');
    }

    /**
     * parse @overwrite tag
     */
    protected function parseOverwrite()
    {
        $this->pregReplace('#@overwrite\b#', '<?php $__compile->overwriteSection();?>');
    }

    /**
     * parse @endsection tag
     */
    protected function parseEndsection()
    {
        $this->pregReplace('#@endsection\b#', '<?php $__compile->stopSection();?>');
    }

    /**
     * parse @foreach tag
     */
    protected function parseForeach()
    {
        $this->pregReplace('#@foreach\(([^)]+)\)#', '<?php foreach($1){ ?>');
    }

    /**
     * parse @endforeach tag
     */
    protected function parseEndforeach()
    {
        $this->pregReplace('#@endforeach#', '<?php } ?>');
    }

    /**
     * parse  @for tag
     */
    protected function parseFor()
    {
        $this->pregReplace('#@for\(([^\)]+)\)#', '<?php for($1){?>');
    }

    /**
     * parse @endfor tag
     */
    protected function parseEndfor()
    {
        $this->pregReplace('#@endfor#', '<?php } ?>');
    }

    /**
     * parse @while tag
     */
    protected function parseWhile()
    {
        $this->pregReplace('#@while\(([^)]+)\)#', '<?php while($1){?>');
    }

    /**
     * parse @endwhile tag
     */
    protected function parseEndWhile()
    {
        $this->pregReplace('#@endwhile#', '<?php } ?>');
    }

    /**
     * parse @if tag
     */
    protected function parseIf()
    {
        $this->pregReplace('#@if\(([^)]+)\)#', '<?php if($1){ ?>');
    }

    /**
     * parse @elseif tag
     */
    protected function parseElseIf()
    {
        $this->pregReplace('#@elseif\(([^)]+)\)#', '<?php if($1){ ?>');
    }

    /**
     * parse @else tag
     */
    protected function parseElse()
    {
        $this->pregReplace('#@else#', '<?php }else{ ?>');
    }

    /**
     * parse @endif tag
     */
    protected function parseEndIf()
    {
        $this->pregReplace('#@endif#', '<?php } ?>');
    }

    /**
     * parse {{ }} tag
     */
    protected function parseEcho()
    {
        $this->pregReplace('#\{\{([^}]+)\}\}#', '<?php echo htmlentities($1); ?>');
    }

    /**
     * parse @{{ }} tag
     */
    protected function parseRawEcho()
    {
        $this->pregReplace('#@\{\{([^}]+)\}\}#', '<?php cho $1; ?>');
    }

    /**
     * parse {{--   --}} tag
     */
    protected function parseComment()
    {
        $this->pregReplace('#\{\{\--[\s\S]*--}\}#', '');
    }

    /**
     *  get include text
     *
     * @param $filename string
     * @return string
     */
    private function readFile($filename)
    {
        return file_get_contents($filename);
    }

    /**
     * @param $pattern mixed
     * @param $replacement mixed
     */
    private function pregReplace($pattern, $replacement)
    {
        if( $replacement instanceof \Closure )
            $this->parsedText=preg_replace_callback($pattern, $replacement, $this->parsedText);
        else
            $this->parsedText=preg_replace($pattern, $replacement, $this->parsedText);
    }

    /**
     *  include('layout/base')  /path/to/file/layout/base
     * @param $name
     * @return string
     */
    private function shortNameToFilename($name)
    {
        return dirname($this->filename) . '/' . $name . '.blade.php';
    }

    private function getCommonRegex($type)
    {
        switch($type)
        {
            case 'path':
                return '\'([\/\.\w]+)\'';
            case 'name':
                return '\'(\w+)\'';
            case 'variable':
                return '[a-zA-Z0-9_\x7f-\xff]*';
        }
    }

}
