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

/**
 * @return Body
 */
function newBody()
{
   return new Body();
}

class Body implements ClauseInterface
{
    use ClauseTrait;

    public $container=[];

    public function addFilter(Filter $filter)
    {
        $this->container['filter']=$filter;
        return $this;
    }

    public function addQuery(Query $query)
    {
        $this->container['query']=$query;
        return $this;
    }

    public function addSort()
    {

    }


    /**
     * @return mixed
     */
    public function toArray()
    {
        $clauses=[];
        foreach($this->container as $key=>$value)
        {
            $clauses[$key]=$value->toArray();
        }
        return $clauses;
    }
}
