<?php
/*
* This file is part of the Scaffold package.
*
* (c) bingxia liu  <xiabingliu@163.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace Scaffold\Database\Query\ElasticSearch;

class Filter implements ClauseInterface
{
    use ClauseTrait;

    public function addBool(Boolean $bool)
    {
        $this->container['bool']=$bool;
        return $this;
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        $clauses=[];
        foreach($this->container as $key=>$clause)
        {
            $clauses[$key]=$clause->toArray();
        }
        return $clauses;
    }

}