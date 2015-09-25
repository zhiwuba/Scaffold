<?php
/**
 * User: liubingxia
 * Date: 15-9-20
 * Time: 下午12:29
 *  Tree relation.
 */
namespace Scaffold\Database\Query;

use \Scaffold\Helper\Utility;

class Condition
{
    public function __construct($expression, $values)
    {
        $this->expression=$expression;
        $this->values=$values;
    }

    /**
     *  @var string
     */
    public $expression;

    /**
     *  @var Array placeholder value.
     */
    public $values=[];

}


class Where
{
    public static $relationOR=' OR ';
    public static $relationAND=' AND ';

    /**
    *  @enum or and
    */
    protected $relationOperate;

    /**
    *  @var Where[] children relation object
    */
    protected $subWhere=[];

    /**
    *  @var Condition[] children condition object.
    */
    protected $subCondition=[];


    public function addSubCondition(Condition $condition)
    {
        $this->subCondition[]=$condition;
    }

    public function getSubCondition()
    {
        return $this->subCondition;
    }

    public function addSubWhere(Where $relation)
    {
        $this->subWhere[]=$relation;
    }

    public function getSubWhere()
    {
        return $this->subWhere;
    }

    /**
    *  orWhere
     * eg: orWhere()->orWhere()
    */
    public function orWhere()
    {
        $args=func_get_args();
        if( $this->relationOperate!==static::$relationAND)
        {
            $this->relationOperate=static::$relationOR;
            call_user_func_array(array($this, 'addWhere'), $args);
            return $this;
        }
        else
        {
            throw new \Exception("or relation only support orWhere.");
        }
    }

    /**
     * andWhere
    *  eg: andWhere()->andWhere()
    */
    public function andWhere()
    {
        $args=func_get_args();
        if( $this->relationOperate!=static::$relationOR )
        {
            $this->relationOperate=static::$relationAND;
            call_user_func_array(array($this, 'addWhere'), $args);
            return $this;
        }
        else
        {
            throw new \Exception("and relation only support andWhere");
        }
    }

    /**
    *  add orWhere or andWhere.
    */
    protected function addWhere()
    {
        $args=func_get_args();

        $relation=new Where();

        if( is_object($args[0]) && is_callable($args[0]) )
        {
            $callback=$args[0];
            $callback($relation);
        }
        else if( is_string($args[0]) )
        {
            $expression=array_shift($args);
            $values=Utility::arrayFlatten($args);
            $condition=new Condition($expression, $values);
            $relation->addSubCondition($condition);
        }
        $this->addSubWhere($relation);
        return $this;
    }


    /**
    *   assemble
     * @return array($expression, $bindings)
    */
    public function assemble()
    {
        $bindings=[];
        $parts=[];
        foreach($this->subWhere as $relation)
        {
            list($childExp, $childValues)=$relation->assemble();
            $parts[]='(' . $childExp  . ')';
            $bindings=array_merge($bindings, $childValues);
        }

        $conditionsExp=[];
        foreach($this->subCondition as $condition)
        {
            $conditionsExp[]=$condition->expression;
            $bindings=array_merge($bindings, $condition->values);
        }

        $parts[]=implode($this->relationOperate, $conditionsExp);

        $parts=array_filter($parts, function($part){
            return !empty($part);
        });

        $expression=implode($this->relationOperate, $parts);

        return array($expression, $bindings);
    }
}
