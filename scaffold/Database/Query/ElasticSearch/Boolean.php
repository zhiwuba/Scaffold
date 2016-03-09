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
 *
 */
class Boolean implements ClauseInterface
{
    use ClauseTrait;

    protected $container;

    public function addShould(Should $should)
    {
        $this->container['should']=$should;
        return $this;
    }

    public function addMust(Must $must)
    {
        $this->container['must']=$must;
        return $this;
    }

    public function addMustNot(MustNot $mustNot)
    {
        $this->container['must_not']=$mustNot;
        return $this;
    }

    public function addFilter()
    {
        //TODO
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

/**
 * @return Boolean
 */
function newBoolean()
{
    return new Boolean();
}