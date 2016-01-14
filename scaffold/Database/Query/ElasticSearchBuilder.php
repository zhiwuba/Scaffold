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
	*  switch index to operate.
	 * @param string $index
	 * @return $this
	*/
	public function switchIndex($index)
	{
		$this->index=$index;
		return $this;
	}

	/**
	* @var $routing
	*/
	public function setRouting($routing)
	{
		$this->routing=$routing;
	}

	/**
	* @param string $id
	*/
	public function setId($id)
	{
		$this->id=$id;
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
		if ( $this->scenario==='select' )
        {
			$result = $this->search();
			return $result;
		}
		else
		{
			throw new BuilderException("ElasticSearch fetch only support select");
		}
    }

    public function fetchAll()
    {
        if( $this->scenario==='select' )
        {
			$result=$this->search();
            return $result;
        }
        else
        {
            throw new BuilderException("ElasticSearch fetchAll only support select");
        }
    }

    /**
     *  aggregation
     */
    public function count()
    {
        $params=$this->assembleSelect();
        $result=static::getConnection()->count($params);
        return $result;
    }

    public function max($column)
    {
        $this->orders[]=[$column, 'desc'];
        $this->take=1;
		$result=$this->search();
        return $result;
    }

    public function min($column)
    {
        $this->orders[]=[$column, 'asc'];
        $this->take=1;
		$result=$this->search();
		return $result;
    }

    public function sum($column)
    {

    }

    protected function assembleSelect()
    {
		$body=[];

        if( !empty($this->selects) ) {
            $body['_source']=$this->selects;
        }

		$where=$this->assembleWhere($this->where);
		if( !empty($where) ) {
			$body['query']['filtered']['filter']=$where;
		}

        if( !empty($this->groups) ) {
            //https://www.elastic.co/guide/en/elasticsearch/reference/1.4/_executing_aggregations.html
        }

		if( !empty($this->orders) ) {
			foreach($this->orders as $field=>$order) {
				$body['sort'][]=[$field=>['order'=>$order]];
			}
		}

		if( !empty($this->skip) ) {
			$body['from']=$this->skip;
		}

		if( !empty($this->take) ) {
			$body['size']=$this->take;
		}

		$params=$this->getBaseParam();
		$params['body']=$body;
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
		$params['body']=$this->data;
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
	 * @return array($expression, $bindings)
	 */
	protected function assembleWhere($where)
	{
		$parts=[];
		$operate=$where->getRelationOperate();
		foreach($where->getSubWhere() as $subWhere)
		{
			$segment=$this->assembleWhere($subWhere);
			if( $operate==Where::$relationAND )
			{
				$parts["must"][]=$segment;
			}
			else if( $operate==Where::$relationOR )
			{
				$parts["should"][]=$segment;
			}
		}

		foreach($where->getSubCondition() as $condition)
		{
			$segment=$this->buildCondition($condition);
			if( $condition->isNegative() )
			{
				$parts["must_not"][]=$segment;
			}
			else if( $condition->isFuzzy() )
			{
				$parts['query']=$segment;
			}
			else if( $condition->relation==Where::$relationAND )
			{
				$parts["must"][]=$segment;
			}
			else if( $condition->relation==Where::$relationOR )
			{
				$parts["should"][]=$segment;
			}
		}
		return ["bool"=>$parts];
	}

	/**
	*  build condition
	 * @param $condition Condition
	 * @return array
	*/
	protected function buildCondition(Condition $condition)
	{
		$express=[];
		$values=$condition->values;
		$name=$condition->name;
		switch( $condition->operate )
		{
			case '=':
				if( in_array($values[0], ['null', 'NULL', null]) )
				{
					$express['missing']=['field'=>$name];
				}
				else
				{
					$express['term']=[$name => $values[0]];
				}
				break;
			case '!=':
				if( in_array($values[0], ['null', 'NULL', null]) )
				{
					$express['exists']=['field'=>$name];
				}
				else
				{
					$express['term']=[$name=>$values[0]];
				}
				break;
			case '>':
				$express['range'][$name][]=['gt'=>$values[0]];
				break;
			case '>=':
				$express['range'][$name][]=['gte'=>$values[0]];
				break;
			case '<':
				$express['range'][$name][]=['lt'=>$values[0]];
				break;
			case '<=':
				$express['range'][$name][]=['lte'=>$values[0]];
				break;
			case 'in':
				$express['terms'][$name]=$values;
				break;
			case 'not in':
				$express['terms'][$name]=$values;
				break;
			default:
				break;
		}
		return $express;
	}

    private function getBaseParam()
    {
		$param=[
			'routing'=>$this->routing,
			'index'=>$this->index,
			'type'=>$this->table,
		];
        return $param;
    }
}
