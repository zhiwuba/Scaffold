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


class Query implements ClauseInterface
{
    use ClauseTrait;

    public function addBool(Boolean $bool)
    {
        $this->container['bool']=$bool;
        return $this;
    }

    public function addFilter(Filter $filter)
    {
        $this->container['filter']=$filter;
        return $this;
    }

    public function matchAll()
    {
        $this->container['match_all']=[];
        return $this;
    }

    /**
     * @param $multiMatch array
     * @return $this
     */
    public function multiMatch($multiMatch)
    {
        $this->container['multi_match']=$multiMatch;
        return $this;
    }

    /**
     * @param $match array
     * @return $this
     */
    public function match($match)
    {
        $this->container['match']=$match;
        return $this;
    }

    /**
     * @param $query array
     * @return $this
     */
    public function common($query)
    {
        $this->container['common']=$query;
        return $this;
    }

    /**
     * @param $queryString array
     * @return $this
     */
    public function queryString($queryString)
    {
        $this->container['query_string']=$queryString;
        return $this;
    }

    /**
     * @param $min
     * @return $this
     */
    public function addMinShouldMatch($min)
    {
        $this->container['minimum_should_match']=$min;
        return $this;
    }
}