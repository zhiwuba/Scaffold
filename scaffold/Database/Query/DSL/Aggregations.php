<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace  Scaffold\Database\Query\DSL;

/**
 * Class Aggregations
 * @package Scaffold\Database\Query\DSL
 * @method Aggregation  avg(string $name, string $field)  A single-value metrics aggregation that computes the average of numeric values that are extracted from the aggregated documents
 * @method Aggregation  sum($name, $field)
 * @method Aggregation  max($name, $field)
 * @method Aggregation  min($name, $field)
 * @method Aggregation  count($name, $field)
 * @method Aggregation  stats($name, $field)
 * @method Aggregation  percentile($name, $field)
 *
 * TODO  what is the different between aggregation and sorting?
 */
class Aggregations implements ClauseInterface
{
    use ClauseTrait;

    /**
     * @param $name string  the name of aggregation
     * @param $field
     * @return $this
     */
    public function group( $name, $field )
    {
        $this->container[$name]=(new Aggregation('terms', $field))->field();
        return $this;
    }

    /**
     * TODO
     * @param $type
     * @return $this
     */
    public function cardinality($type)
    {
        return $this;
    }

    public function extendedStats($name, $field)
    {
        $agg=new Aggregation('extended_stats', $field);
        $this->container[$name]=$agg->field();
        return $agg;
    }

    /**
     *  TODO
     */
    public function topHits()
    {
        return $this;
    }

    /**
     * @param Aggregations $aggregations
     * @return $this
     */
    public function addAggregation(Aggregations $aggregations)
    {
        $this->container['aggregations']=$aggregations;
        return $this;
    }

    /**
     * @param string $type
     * @param array $arguments
     * @return Aggregation
     * @throws \Exception
     */
    function __call($type, $arguments)
    {
        $name=array_shift($arguments);
        $field=array_shift($arguments);

        switch($type)
        {
            case 'max':
            case 'min':
            case 'sum':
            case 'count':
            case 'stats':
            case 'avg':
            {
                $agg=new Aggregation($type, $field);
                $this->container[$name]=$agg->field();
                return $agg;
            }
            default:
                throw new \Exception("undefined function $name");
        }
    }
}

