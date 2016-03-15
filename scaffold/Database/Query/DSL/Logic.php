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


class Logic implements ClauseInterface
{
    use ClauseTrait;

    public function addTerm(Term $term)
    {
        $this->container[]=$term;
        return $this;
    }

    public function addBool(Boolean $bool)
    {
        //$this->container['bool']=$bool;
        $this->container[]=['bool'=>$bool]; //TODO
        return $this;
    }

    /**
     * TODO
     * @return mixed
     */
    public function toArray()
    {
        $items=[];
        foreach($this->container as $key=>$item)
        {
            if( is_array($item) ) {
                $items[]=[key($item)=>current($item)->toArray()];
            }
            else{
                $items[$key]=$item->toArray();
            }
        }
        return $items;
    }
}