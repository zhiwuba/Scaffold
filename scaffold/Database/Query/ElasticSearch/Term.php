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


class Term implements ClauseInterface
{
    use ClauseTrait;

    protected $container=[];

    /**
     *  Find documents which contain the exact term specified in the field specified.
     * @param $field
     * @param $value
     * @return $this
     */
    public function term($field, $value)
    {
        $this->container=['term'=>[$field=>$value]];
        return $this;
    }

    /**
     * Find documents which contain any of the exact terms specified in the field specified.
     * @param $field string
     * @param $values array
     * @return $this
     */
    public function terms($field, $values)
    {
        $this->container=['terms'=>[$field=>$values]];
        return $this;
    }

    /**
     * Find documents where the field specified contains any non-null value.
     * @param $field
     * @return $this
     */
    public function exists($field)
    {
        $this->container=['exists'=>['field'=>$field]];
        return $this;
    }

    /**
     * Find documents where the field specified does is missing or contains only null values.
     * @param $field
     * @return $this
     */
    public function missing($field)
    {
        $this->container=['missing'=>['field'=>$field]];
        return $this;
    }

    /**
     *  Find documents with the specified type and IDs.
     * @param $type string
     * @param $values array
     * @return $this
     */
    public function ids($type, $values)
    {
        $this->container=['ids'=>['type'=>$type, 'values'=>$values]];
        return $this;
    }

    /**
     * Find documents of the specified type.
     * @param $type
     * @return $this
     */
    public function type($type)
    {
        $this->container=['type'=>['value'=>$type]];
        return $this;
    }

    /**
     * Levenshtein edit distance for string fields
     * @param $field
     * @param $value
     * @return $this
     */
    public function fuzzy($field, $value)
    {
        $this->container=['fuzzy'=>[$field=>$value]];
        return $this;
    }

    /**
     * Find documents where the field specified contains terms which match the regular expression specified
     * @param $field
     * @param $regex
     * @return $this
     */
    public function regexp($field, $regex)
    {
        $this->container=['regexp'=>["$field.first"=>$regex]]; //TODO first?
        return $this;
    }

    /**
     * Find documents where the field specified contains terms which match the pattern specified,
     * where the pattern supports single character wildcards (?) and multi-character wildcards (*)
     * @param $field
     * @param $value
     * @return $this
     */
    public function wildcard($field, $value)
    {
        $this->container=['wildcard'=>[$field=>$value]];
        return $this;
    }

    /**
     * Find documents where the field specified contains terms which being with the exact prefix specified.
     * @param $key
     * @param $value
     * @return $this
     */
    public function prefix($key, $value)
    {
        $this->container=['prefix'=>[$key=>$value]];
        return $this;
    }

    /**
     * @param $field
     * @param $value
     */
    public function match($field, $value)
    {
        $this->container=['match'=>[$field=>$value]];
        return $this;
    }


    /**
     * @return mixed
     */
    public function toArray()
    {
        return $this->container;
    }
}
