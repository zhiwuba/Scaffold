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
 * Class Boolean
 * @package Scaffold\Database\Query\ElasticSearch
 */
class Boolean implements ClauseInterface
{
    use ClauseTrait;

    public function addShould(Logic $should)
    {
        $this->container['should']=$should;
        return $this;
    }

    public function addMust(Logic $must)
    {
        $this->container['must']=$must;
        return $this;
    }

    public function addMustNot(Logic $mustNot)
    {
        $this->container['must_not']=$mustNot;
        return $this;
    }

    public function addFilter(Filter $filter)
    {
        $this->container['filter']=$filter;
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