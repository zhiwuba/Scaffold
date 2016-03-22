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

class Aggregation implements ClauseInterface
{
    use ClauseTrait;

    private $type;

    private $field;

    public function __construct($type, $field)
    {
        $this->type=$type;
        $this->field=$field;
    }

    public function field()
    {
        $this->container[$this->type] = ['field'=>$this->field];
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
}

