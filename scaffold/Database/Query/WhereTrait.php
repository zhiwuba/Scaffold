<?php
/*
* This file is part of the Scaffold package.
*
* (c) bingxia liu  <xiabingliu@163.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Scaffold\Database\Query;

trait WhereTrait
{
    /**
     * @param Where $where
     * @return array($expression, $bindings)
     */
    protected function assembleWhere($where)
    {
        $bindings=[];
        $parts=[];
        foreach($where->getSubWhere() as $relation)
        {
            list($childExp, $childValues)=$this->assembleWhere($relation);
            $parts[]='(' . $childExp  . ')';
            $bindings=array_merge($bindings, $childValues);
        }

        $conditionsExp=[];
        foreach($where->getSubCondition() as $condition)
        {
            $conditionsExp[]=$this->assembleCondition($condition);
            $bindings=array_merge($bindings, $condition->values);
        }

        $operate=' ' . $where->getRelationOperate() . ' ';
        $parts[]=implode($operate, $conditionsExp);

        $parts=array_filter($parts, function($part){
            return !empty($part);
        });

        $expression=implode($operate, $parts);

        return array($expression, $bindings);
    }

    protected function assembleCondition(Condition $condition)
    {
        $parts=[];
        $parts[]=$condition->name;
        $parts[]=$condition->operate;
        if(in_array($condition->operate, ['in', 'not in']))
        {
            $parts[]='(' . implode(',', array_fill(0, count($condition->values), '?')) . ')';
        }
        else
        {
            $parts[]='?';
        }

        $expression=implode(' ', $parts);
        return $expression;
    }

}