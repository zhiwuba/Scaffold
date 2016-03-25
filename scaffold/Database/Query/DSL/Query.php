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

    public function multiMatch($fields, $query)
    {
        $this->container['multi_match']=[
            'query'=>$query,
            'fields'=>$fields
        ];
        return $this;
    }

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