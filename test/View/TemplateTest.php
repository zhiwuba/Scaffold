<?php
/*
* This file is part of the Scaffold package.
*
* (c) bingxia liu  <xiabingliu@163.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Test;

use Scaffold\View\Template;

class TemplateTestCase extends TestCase
{
    public function testTemplate()
    {
        $template=new Template("/home/explorer/workspace/Scaffold/test/View/main.blade.php");
        echo $template->compile();
    }
}
