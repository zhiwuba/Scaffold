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

	public function execute()
    {
		if( in_array($this->scenario, ['insert', 'update', 'delete']) )
		{
			$result=$this->assemble();
			return $result;
		}
		else
		{
			throw new \Exception("execute only support delete、update and insert!");
		}
    }

    public function fetch()
	{
		if (in_array($this->scenario, ['select'])) {
			$result = $this->assemble();
			return $result;
		}
		else
		{
			throw new \Exception("fetch only support select");
		}
    }

    public function fetchRow()
    {

    }

    public function fetchPair()
    {

    }

    public function fetchGroup()
    {

    }

    public function fetchAll()
    {

    }

    /**
     *  aggregation
     */
    public function count()
    {

    }

    public function max($column)
    {

    }

    public function min($column)
    {

    }

    public function sum($column)
    {

    }

    protected function assembleSelect()
    {
		$body=[];

		/** @var \Elasticsearch\Client $client **/
		$client=static::getConnection();

		$where=$this->assembleWhere($this->where);
		if( !empty($where) ) {
			$body['query']['filtered']['filter']=$where;
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

		$param=[
			'routing'=>$this->routing,
			'index'=> $this->index,
			'type'=> $this->table,
			'body'=>	$body
		];
		//$result=$client->search($param);
		//return $result;
        return $param;
    }

    protected function assembleInsert()
    {
		/** @var \Elasticsearch\Client $client **/
		$client=static::getConnection();
		$param=[
			'routing'=>'',
			'index'=> $this->index,
			'type' => $this->table,
			'id'	  => $this->id,
			'body'=>[
				$this->data
			]
		];
		$result=$client->create($param);
    	return $result;
	}

    protected function assembleUpdate()
    {
		/** @var \Elasticsearch\Client $client **/
		$client=static::getConnection();
		$param=[
			'routing'=>'',
			'index'=>$this->index,
			'type'=>$this->table,
			'id'=>$this->id,
			'body'=>[
				$this->data
			]
		];
		$result=$client->update($param);
    	return $result;
	}

    protected function assembleDelete()
    {
		/** @var \Elasticsearch\Client $client **/
		$client=static::getConnection();
		$param=[
			'routing'=>'',
			'index'=>$this->index,
			'type'=>$this->table,
			'id'=>$this->id,
		];
		$result=$client->delete($param);
		return $result;
    }

	/**
	 * @param Where $where
	 * @return array($expression, $bindings)
	 */
	protected function assembleWhere($where)
	{
		$parts=[];
        $relation=$where->getRelationOperate();
		foreach($where->getSubWhere() as $subWhere) {
			$childPart=[];
			$childExp=$this->assembleWhere($subWhere);
			if( $relation=='OR' ) {
				$childPart['bool']['should'] = $childExp;
			}
			else if( $relation=='AND' ) {
				$childPart['bool']['must'] = $childExp;
			}
			$parts[]=$childPart;
		}

		foreach($where->getSubCondition() as $condition)
		{
			$parts[]=$this->transformCondition($condition);
		}

		return $parts;
	}

	protected function transformCondition(Condition $condition)
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
					$express['must_not']['term']=[$name=>$values[0]];
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
				$express['must_not']['terms'][$name]=$values;
				break;
			default:
				break;
		}
		return $express;
	}
}
