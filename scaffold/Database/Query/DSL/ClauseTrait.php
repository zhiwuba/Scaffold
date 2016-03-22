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

trait ClauseTrait
{
    /**
     * @var ClauseInterface[]|string[]
     */
    protected $container=[];

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->container);
    }

    /**
     * @return static
     */
    public static function O()
    {
        return new static;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->toArrayHelper($this->container);
    }

    /**
     * @param $items array
     * @return array
     */
    private function toArrayHelper($items)
    {
        $ret=[];
        foreach($items as $key=>$value)
        {
            if( $value instanceof Aggregation)
            {
                $ret[$key]=$value->toArray();
            }
            elseif( is_array($items) )
            {
                $ret[$key]=$this->toArrayHelper($value);
            }
            else
            {
                $ret[$key]=$value;
            }
        }
        return $ret;
    }


}

