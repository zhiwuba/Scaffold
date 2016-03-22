<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scaffold\Database\Query\DSL;

class Script implements ClauseInterface
{
    use ClauseTrait;

    public function script($name, $exp, $params=[])
    {
        $script=[];
        $script[$name]=$exp;
        if( !empty($params) ){
            $script['params']=$params;
        }
        $this->container['script']=$script;
        return $this;
    }
}


