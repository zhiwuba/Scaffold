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

use Scaffold\Database\Profile\Profile;
use Scaffold\Database\Query\DSL\Aggregation;
use Scaffold\Database\Query\DSL\Boolean;
use Scaffold\Database\Query\DSL\Body;
use Scaffold\Database\Model\ElasticSearchModel;
use Scaffold\Database\Query\DSL\Filter;
use Scaffold\Database\Query\DSL\Logic;
use Scaffold\Database\Query\DSL\Term;
use Scaffold\Helper\Utility;

class ElasticSearchBuilder extends Builder
{
	/**
	 * @var \ElasticSearch\Client
	 */
	public static $connection;

	/**
	 * @var Profile
	 */
	public static $profile;

	/**
	* @var string
	*/
	protected $routing;

	/**
	 * elasticsearch index.
	* @var  string $index
	*/
	protected $index;

	/**
	* @var string $id
	*/
	protected $id;

    /**
     * @var $aggregation Aggregation
     */
    protected $aggregation;


    protected $aggregation_type='group';


    /**
     * ElasticSearchBuilder constructor.
     * @param $tableName
     */
    public function __construct($tableName)
    {
        $this->aggregation=new Aggregation(); //TODO
        parent::__construct($tableName);
    }


    public function getBaseParam()
	{
		$param=[
			'routing'=>$this->routing,
			'index'=>$this->index,
			'type'=>$this->table,
		];
		return $param;
	}

	public function setBaseParam($params)
	{
		if( isset($params['index']) )
			$this->index=$params['index'];
		if(isset($params['routing']))
			$this->routing=$params['routing'];
		if(isset($params['id']))
			$this->id=$params['id'];
		return $this;
	}

    /**
    * @return  \Elasticsearch\Client
     */
    public static function getConnection()
    {
        return static::$connection;
    }

	/**
	 * @param $connection \Elasticsearch\Client
	 */
	public static function setConnection($connection)
	{
		static::$connection=$connection;
	}

    public function execute()
    {
        $connection=static::getConnection();
        switch($this->scenario)
        {
            case 'insert':
                $params=$this->assembleInsert();
                $result=$connection->create($params);
                break;
            case 'update':
                $params=$this->assembleUpdate();
                $result=$connection->update($params);
                break;
            case 'delete':
                $params=$this->assembleDelete();
                $result=$connection->delete($params);
                break;
            default:
                throw new BuilderException("execute only support delete、update and insert!");
        }
        return $result;
    }

	public function search()
	{
		$connection=static::getConnection();
		$params=$this->assemble();
		$result=$connection->search($params);
		return $result;
	}

    public function fetch()
	{
        $this->restrictScenario('select');

        $response = $this->search();
        $models=$this->responseToModels($response);
        return count($models)? $models[0] : null ;
    }

    public function fetchAll()
    {
        $this->restrictScenario('select');

        $response=$this->search();
        $models=$this->responseToModels($response);
        return $models;
    }

    /**
     *  aggregation
     */
    public function count()
    {
        $params=$this->assembleSelect();
        $result=static::getConnection()->count($params);
        return $result['count'];
    }

    public function max($column)
    {
        $this->orders[$column]='desc';
        $this->take=1;
		$result=$this->search();
        return Utility::arrayGet($result, 'hits.hits.0._source.' . $column);
    }

    public function min($column)
    {
        $this->orders[$column]= 'asc';
        $this->take=1;
		$result=$this->search();
        return Utility::arrayGet($result, 'hits.hits.0._source.'.$column);
    }

    public function sum($column)
    {
        $this->aggregation_type='sum';
        $result=$this->search();
        return $result;
    }

    protected function assembleSelect()
    {
        $body=new Body();

        if( !empty($this->selects) ) {
            $body->addSource($this->selects);
        }

		$bool=$this->assembleWhere($this->where);
		if( !$bool->isEmpty() ) {
            $body->addFilter((new Filter())->addBool($bool));
		}

        if( !empty($this->groups) ) {
            $root=$body;
            foreach($this->groups as $field) {
                $name=$field .'_'. $this->aggregation_type;
                $agg=call_user_func_array([(new Aggregation()), $this->aggregation_type], [$field])->field(20);
                $root->addAggregation($name, $agg);
                $root=$agg;
            }
        }

		if( !empty($this->orders) ) {
			foreach($this->orders as $field=>$order) {
                $body->addSort($field, $order);
			}
		}

		if( !empty($this->skip) || !empty($this->take) ) {
            $body->addFromSize($this->skip, $this->take);
		}

		$params=$this->getBaseParam();
		$params['body']=$body->toArray();
		return $params;
    }

    protected function assembleInsert()
    {
		$params=$this->getBaseParam();
		$params['id']=$this->id;
		$params['body']=$this->data;
		return $params;
	}

    protected function assembleUpdate()
    {
		$params=$this->getBaseParam();
		$params['id']=$this->id;
        if( !empty($this->data) )
        {
            $params['body']['doc']=$this->data;
        }

		$scripts=[];
		$script_params=[];
		foreach($this->getIncrements() as $key=>$value)
		{
			$count_name='count_'.$key;
			if($value>0) {
				$scripts[]="ctx._source.$key+=$count_name";
			}
			elseif($value<0){
				$scripts[]="ctx._source.$key-=$count_name";
			}
			else{
				continue;
			}
			$script_params[$count_name]= abs($value);
		}

		if( !empty($scripts) && !empty($script_params) ) {
			$params['body']['script']=implode(';', $scripts);
			$params['body']['params']=$script_params;
		}

		return $params;
	}

    protected function assembleDelete()
    {
		$params=$this->getBaseParam();
		$params['id']=$this->id;
		return $params;
    }

	/**
	 * @param Where $where
	 * @return \Scaffold\Database\Query\DSL\Boolean;
	 */
	protected function assembleWhere($where)
	{
        $bool=new Boolean();
        $logic=new Logic();
        $notLogic=new Logic();

        if( !empty($where->getSubWhere()) )
        {
            foreach($where->getSubWhere() as $subWhere)
            {
                $logic->addBool($this->assembleWhere($subWhere));
            }
        }

		foreach($where->getSubCondition() as $condition)
		{
            $term=$this->buildCondition($condition);
            if( $condition->isNot() )
            {
                $notLogic->addTerm($term);
            }
            else
            {
                $logic->addTerm($term);
            }
		}

        if( !$logic->isEmpty() )
        {
            $operate=$where->getRelationOperate();
            if( $operate==Where::$relationAND )
            {
                $bool->addMust($logic);
            }
            else if( $operate==Where::$relationOR )
            {
                $bool->addShould($logic);
            }
        }

        if( !$notLogic->isEmpty() )
        {
            $bool->addMustNot($notLogic);
        }

        return $bool;
	}

	/**
	*  build condition
	 * @param $condition Condition
	 * @return Term
	*/
	protected function buildCondition(Condition $condition)
	{
        $term=new Term();
        $field=$condition->name;
		$values=$condition->values;
		switch( $condition->operate )
		{
			case '=':
				if( in_array($values[0], ['null', 'NULL', null]) )
				{
                    $term->missing($field);
				}
				else
				{
                    $term->term($field, $values[0]);
				}
				break;
			case '!=':
				if( in_array($values[0], ['null', 'NULL', null]) )
				{
                    $term->exists($field);
				}
				else
				{
                    $term->term($field, $values[0]);
				}
				break;
			case '>':
                $term->range($field, ['gt'=>$values[0]]);
				break;
			case '>=':
                $term->range($field, ['gte'=>$values[0]]);
				break;
			case '<':
                $term->range($field, ['lt'=>$values[0]]);
				break;
			case '<=':
                $term->range($field, ['lte'=>$values[0]]);
				break;
			case 'in':
                $term->terms($field, $values);
				break;
			case 'not in':
                $term->terms($field, $values);
				break;
            case 'regexp':
                $term->regexp($field, $values[0]);
                break;
			default:
				break;
		}
		return $term;
	}

	protected function responseToModels($response)
    {
        if( !empty($this->model) )
        {
            $models=[];
            foreach($response['hits']['hits'] as $hit)
            {
                /** @var ElasticSearchModel $model */
                $model=call_user_func([$this->model, 'instance'], $hit['_source']);
                $models[]=$model;
            }
            return $models;
        }
        else
        {
            return $response['hits']['hits'];
        }
    }
}
