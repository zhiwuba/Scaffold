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
 * Class Aggregation
 * @package Scaffold\Database\Query\DSL
 * @method Aggregation group(string $field)
 * @method Aggregation count(string $field)
 * @method Aggregation  avg(string $field)  A single-value metrics aggregation that computes the average of numeric values that are extracted from the aggregated documents
 * @method Aggregation  sum(string $field)
 * @method Aggregation  max(string $field)
 * @method Aggregation  min(string $field)
 * @method Aggregation  stats(string $field)
 * @method Aggregation  percentile(string $field)
 *
 * TODO  what is the different between aggregation and sorting?
 */
class Aggregation implements ClauseInterface
{
    use ClauseTrait;

    private $field;

    private $type;

    public function __construct()
    {
    }

    /**
     * TODO
     */
    public function cardinality(){
        return $this;
    }

    /**
     *  TODO
     */
    public function topHits(){
        return $this;
    }


    public function field($size=10)
    {
        $this->container[$this->type] = ['field'=>$this->field, 'size'=>$size];
        return $this;
    }

    /**
     * simple  script
     * @param $script
     * @return $this
     */
    public function script($script)
    {
        $script['field']=$this->field;
        $script['script']=$script;
        $this->container[$this->type]=$script;
        return $this;
    }

    /**
     * @param $type
     * @param $field
     * @param $missing
     * @return $this
     */
    public function missing($missing)
    {
        $this->container[$this->type]=['field'=>$this->field, 'missing'=>$missing];
        return $this;
    }

    /**
     * @param string $name
     * @param Aggregation $aggregation
     * @return $this
     */
    public function addAggregation($name, Aggregation $aggregation)
    {
        $this->container['aggregations'][$name]=$aggregation;
        return $this;
    }


    private function funcMapType($name)
    {
        $map=[
            'group'=>'terms',
            'count'=>'value_count',
            'extendedStats'=>'extended_stats'
        ];
        return isset($map[$name])?$map[$name]:$name;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return Aggregation
     * @throws \Exception
     */
    function __call($name, $arguments)
    {
        $this->field=array_shift($arguments);
        $name=$this->funcMapType($name);
        switch($name)
        {
            case 'max':
            case 'min':
            case 'sum':
            case 'avg':
            case 'stats':
            case 'terms':
            case 'value_count':
            case 'extended_stats':
            {
                $this->type=$name;
                return $this->field();
            }
            default:
                throw new \Exception("undefined callback: $name");
        }
    }
}

