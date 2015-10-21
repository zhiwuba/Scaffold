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

use \Scaffold\Helper\Utility;

class Condition
{
	/**
	* @var array
	*/
	public static $supportOperate=[ '=',  '!=', '>=', '>', '<=', '<', 'in' , 'not in'];

    /**
     *  @var string
     */
    public $name;

	/**
	* @var string
	*/
	public $operate;

    /**
     *  @var Array placeholder value.
     */
    public $values=[];


	/**
	*  __construct
	 * @param $name string
	 * @param $operate string
	 * @param $values array
	*/
	public function __construct($name, $operate , $values)
	{
		$this->name=$name;
		$this->operate=$this->checkOperate($operate);
        $this->values  =$this->checkValues($values);
    }

	/**
	 * check whether support this operate or not.
	* @param $operate string
	 * @return string
	*/
	private function checkOperate($operate)
	{
		$operate=strtolower($operate);
		if( false===array_search($operate, static::$supportOperate ) )
		{
			throw new \InvalidArgumentException("unsupported operate: $operate");
		}
		return $operate;
	}

	/**
	 * check values for operate
	* @param $values array
	 * @return array
	*/
	private function checkValues($values)
	{
		$operates=array_slice(static::$supportOperate, 0, -2);
		if( in_array($this->operate, $operates) ) {
			if (count($values) > 1) {
				throw new \InvalidArgumentException("unsupported values for operate {$this->operate}, and values is" . json_encode($values));
			}
		}
		else {
			if (count($values) == 0) {
				throw new \InvalidArgumentException("unsupported values for operate {$this->operate}, and values is" . json_encode($values));
			}
		}
		return $values;
	}

}


class Where
{
    public static $relationOR='OR';
    public static $relationAND='AND';

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

	public function getRelationOperate()
	{
		return $this->relationOperate;
	}

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
            $name=array_shift($args);
            $operate=array_shift($args);
            $values=Utility::arrayFlatten($args);
            $condition=new Condition($name,$operate, $values);
            $relation->addSubCondition($condition);
        }
        $this->addSubWhere($relation);
        return $this;
    }

}
