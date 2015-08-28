<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-26
 * Time: 下午10:50
 */


class ServerRequestTest extends PHPUnit_Framework_TestCase
{
    protected function isNormalArray($array)
    {
        return array_keys($array)===range(0, count($array)- 1);
    }

    public function normalizeFiles($filePropArray, &$fileArray )
    {
        foreach($filePropArray as $name=>$props)
        {
            if( is_array($props) && $this->isNormalArray($props) )
            {
                foreach( $props as $key=>$val )
                {
                    isset($fileArray[$key])? : $fileArray[$key]=[];
                    $fileArray[$key][$name]=$val;
                }
            }
            else if( is_array($props) )
            {
                isset($fileArray[$name])?: $fileArray[$name]=[];
                $this->normalizeFiles($props, $fileArray[$name]);
            }
            else
            {
                $fileArray[$name]=$props;
            }
        }
    }


    public function testNormalFile()
    {

        $_FILES=array(
            'files' => array(
                'name' => array(
                    0 => 'file0.txt',
                    1 => 'file1.html',
                ),
                'type' => array(
                    0 => 'text/plain',
                    1 => 'text/html',
                ),
            ),
        );

        $_FILES=array(
            'files' => array(
                'name' => 'file0.txt',
                'type' => 'text/plain',
            ),
        );


        $this->normalizeFiles($_FILES, $result);
        print_r($result);
    }

}

