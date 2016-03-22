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


class Body implements ClauseInterface
{
    use ClauseTrait;

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

    /**
     *  control how the _source field is returned with every hit.
     *  @param $source mixed false|string|array
     *  @return $this
     */
    public function addSource($source)
    {
        $this->container['_source']=$source;
        return $this;
    }

    /**
     * @param $field
     * @param $order  string  asc|desc
     * @param $mode string min|max|sum|avg|median
     * @return $this
     */
    public function addSort($field, $order, $mode=null )
    {
        $this->container['sort'][]=[$field=>['order'=>$order, 'mode'=>$mode]];
        return $this;
    }

    /**
     * Pagination of results can be done by using the from and size parameters
     * @param int $from
     * @param int $size
     * @return $this
     */
    public function addFromSize($from=null, $size=null)
    {
        $this->container['from']=$from ?: 0;
        $this->container['size']=$size ?: 100;
        return $this;
    }

    /**
     * @param array $heightLight
     * @return $this
     */
    public function addHeightLight($heightLight)
    {
        $this->container['highlight']=$heightLight;
        return $this;
    }

    /**
     *  like group of sql. but more
     * @param Aggregations $aggregations
     * @return $this
     */
    public function addAggregations(Aggregations $aggregations)
    {
        $this->container['aggregations']=$aggregations;
        return $this;
    }
}
