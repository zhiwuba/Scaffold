<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace  Scaffold\View;
use Scaffold\Exception\ViewException;

/**
 * TODO
 * Class Template Inspire By Blade.
 * @link  http://www.golaravel.com/laravel/docs/5.1/blade/
 * @package Scaffold\View
 */
class Template
{
    /**
     *  template path name.
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $parsedText;

    /**
     *  type section startPos endPos
     *  type:  raw  section virtual_section
     *
     * @var  \stdClass[]
     */
    protected $sections=[];

    public function __construct($name)
    {
        if( is_file($name) )
        {
            $this->name=$name;
            $this->parsedText=$this->getIncludeText($name);
        }
        else
        {
            throw new ViewException("file $name not exist.");
        }
    }

    /**
     *  compile
     * @return string  parsedText
     */
    public function compile()
    {
        $this->compileInclude();
        $this->compileExtends();

        $this->compileIf();
        $this->compileElse();
        $this->compileElseIf();
        $this->compileEndIf();

        $this->compileWhile();
        $this->compileEndWhile();

        $this->compileFor();
        $this->compileEndfor();

        $this->compileForeach();
        $this->compileEndforeach();

        $this->compileEcho();
        $this->compileRawEcho();

        return $this->parsedText;
    }

    public static function createSection($type, $name, $startMatch, $endMatch)
    {
        $section=new \stdClass();
        $section->type=$type;
        $section->name=$name;
        $section->contentRange=[$startMatch[0][1]+strlen($startMatch[0][0]) , $endMatch[0][1]];
        $section->sectionRange=[$startMatch[0][1] ,  $endMatch[0][1]+strlen($endMatch[0][0]) ];
        return $section;
    }

    public function getSections()
    {
        return $this->sections;
    }

    public function getParsedText()
    {
        return $this->parsedText;
    }

    public function parseSection()
    {
        $offset=0;
        $patterns=[
            'section'=> '#@section\(\s*\'(\w+)\'\s*\)#',
            'show'=>    '#@show#',
            'endsection'=>'#@endsection#'
        ];

        $stack=new \SplStack();
        do
        {
            $found=false;
            foreach($patterns as $type=>$pattern)
            {
                $ret=preg_match($pattern, $this->parsedText, $matches, PREG_OFFSET_CAPTURE, $offset);
                if( $ret==0 )
                {
                    continue;
                }
                $found=true;
                $offset= $matches[0][1]+strlen($matches[0][0]);

                if( $type=='section' )
                {
                    $stack[]=$matches;
                }
                else if( $type=='show' )
                {
                    $begin=$stack->pop();
                    $this->sections[]=self::createSection('virtual_section', $begin[1][0], $begin, $matches );
                }
                else if( $type=='endsection' )
                {
                    $begin=$stack->pop();
                    $this->sections[]=self::createSection('section', $begin[1][0], $begin, $matches );
                }
                break;
            }
        }
        while($found);
    }

    /**
     *  combine section
     * @param $extends Template
     */
    protected function combineSection($extends)
    {
        $layoutSections = $extends->getSections();
        $layoutText = $extends->getParsedText();
        $position=0;

        $currentText=$this->getParsedText();
        $resultText='';

        foreach( $layoutSections as $layoutSection )
        {
            if( $layoutSection->type=='virtual_section' )
            {
                $resultText .= substr($layoutText, $position, $layoutSection->sectionRange[0]-$position);

                $section=$this->findSectionByName($layoutSection->name);
                if( $section!==false )
                    $resultText .= substr($currentText, $section->contentRange[0], $section->contentRange[1]-$section->contentRange[0]);
                else
                    $resultText .= substr($layoutText, $layoutSection->sectionRange[0], $layoutSection->sectionRange[1]-$layoutSection->sectionRange[0]);

                $position=$layoutSection->sectionRange[1];
            }
        }

        $resultText .= substr($layoutText, $position);

        $resultText=$this->compileYield($resultText);

        $this->parsedText=$resultText;
    }


    /**
     * parse @yield  @section($key, $value)
     *
     * @param $extendsText string
     * @return string
     */
    public function compileYield($extendsText)
    {
        $patterns=[];
        $replacements=[];
        $ret=preg_match_all('#@section\(\s*\'(\w+)\'\s*,\s*\'([\w]+)\'\s*\)#', $this->parsedText, $matches); //todo
        for($i=0; $i< $ret; $i++)
        {
            $key=$matches[1][$i];
            $value=$matches[2][$i];

            $patterns[]=  '#@yield\(\s*\'' . $key . '\'\s*\)#';
            $replacements[]=$value;
        }

        return preg_replace($patterns, $replacements, $extendsText);
    }


    /**
     * compile @extends tag
     */
    protected function compileExtends()
    {
        $this->parseSection();

        //todo only extend one.
        $ret=preg_match('#@extends\(\'(\S+)\'\)#', $this->parsedText, $matches, PREG_OFFSET_CAPTURE);
        if( $ret!==false && $ret!=0 )
        {
            $extendsFilePath=$this->getAbsolutePath($matches[1][0]);

            $extends=new Template($extendsFilePath);
            $extends->compile();

            $this->combineSection($extends);
        }
    }


    /**
     *  compile @include tag
     */
    protected function compileInclude()
    {
        do
        {
            $ret=preg_match('#@include\(\s*\'(\w+)\'\s*\)#', $this->parsedText, $matches, PREG_OFFSET_CAPTURE); //todo
            if( $ret!=0 )
            {
                $viewName= $this->getAbsolutePath($matches[1][0]);
                $view=new Template($viewName);
                $viewText=$view->compile();
                $this->subStrReplace($viewText, $matches[0][1], strlen($matches[0][0]));
            }
        }
        while($ret!=0);
    }

    /**
     *  compile @foreach tag
     */
    protected function compileForeach()
    {
        $this->pregReplace('#@foreach\(([^)]+)\)#', '<?php foreach($1){ ?>');
    }

    /**
     *  compile @endforeach tag
     */
    protected function compileEndforeach()
    {
        $this->pregReplace('#@endforeach#', '<?php } ?>');
    }

    /**
     * compile  @for tag
     */
    protected function compileFor()
    {
        $this->pregReplace('#@for\(([^\)]+)\)#', '<?php for($1){?>');
    }

    /**
     *  compile @endfor tag
     */
    protected function compileEndfor()
    {
        $this->pregReplace('#@endfor#', '<?php } ?>');
    }

    /**
     * compile @while tag
     */
    protected function compileWhile()
    {
        $this->pregReplace('#@while\(([^)]+)\){#', '<?php while($1){?>');
    }

    /**
     * compile @endwhile tag
     */
    protected function compileEndWhile()
    {
        $this->pregReplace('#@endwhile#', '<?php } ?>');
    }

    /**
     * compile @if tag
     */
    protected function compileIf()
    {
        $this->pregReplace('#@if\(([^)]+)\)#', '<?php if($1){ ?>');
    }

    /**
     * compile @elseif tag
     */
    protected function compileElseIf()
    {
        $this->pregReplace('#@elseif\(([^)]+)\)#', '<?php if($1){ ?>');
    }

    /**
     * compile @else tag
     */
    protected function compileElse()
    {
        $this->pregReplace('#@else#', '<?php }else{ ?>');
    }

    /**
     * compile @endif tag
     */
    protected function compileEndIf()
    {
        $this->pregReplace('#@endif#', '<?php } ?>');
    }

    /**
     * compile {{ }} tag
     */
    protected function compileEcho()
    {
        $this->pregReplace('#\{\{([^}]+)\}\}#', '<?php echo htmlentities($1); ?>');
    }

    /**
     * compile @{{ }} tag
     */
    protected function compileRawEcho()
    {
        $this->pregReplace('#@\{\{([^}]+)\}\}#', '<?php cho $1; ?>');
    }


    /**
     * get absolute path of short name.
     *
     * @param $name
     * @return string
     */
    private function getAbsolutePath($name)
    {
        $name=trim($name);
        $path=dirname($this->name) . '/' . $name . '.blade.php';
        return $path;
    }

    /**
     *  get include text
     *
     * @param $name
     * @return string
     */
    private function getIncludeText($name)
    {
        ob_start();
        require "$name";
        return ob_get_clean();
    }

    /**
     * @param $pattern mixed
     * @param $replacement mixed
     */
    private function pregReplace($pattern, $replacement)
    {
        $this->parsedText=preg_replace($pattern, $replacement, $this->parsedText);
    }

    /**
     * @param $replacement string  replacement content
     * @param $start     int    start position in parsedText
     * @param $length  int    replace length
     */
    private function subStrReplace($replacement, $start, $length)
    {
        $this->parsedText=substr_replace($this->parsedText, $replacement, $start, $length);
    }

    /**
     *  find section by name
     *
     * @param $name
     * @return bool|\stdClass
     */
    private function findSectionByName($name)
    {
        foreach($this->sections as $section) {
            if ($section->name == $name) {
                return $section;
            }
        }
        return false;
    }

    /**
     *  get pattern for @section @include @yield  tag's name.
     *
     * @return string
     */
    private function getNamePattern()
    {
        return '';
    }
}
