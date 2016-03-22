<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scaffold\Database\Model;

use Scaffold\Database\Query\ElasticSearchBuilder;
use Scaffold\Helper\Utility;

class ElasticSearchModel extends Model
{
    protected static $builderClass=ElasticSearchBuilder::class;

    public static $mapping;

    public static $index;

    public static $routingKey;

    /**
     * @inheritDoc
     */
    public static function getBuilder()
    {
        /*** @var ElasticSearchBuilder $builder */
        $builder=parent::getBuilder();
        $builder->setBaseParam(['index'=>static::$index]);
        return $builder;
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        $dirtyData=$this->getDirtyData();

        $baseParam=[
            'id'=>$this->getId(),
            'routing'=>$this->getRouting(),
        ];

        if( empty($this->originalData) )
        {
            $ret=static::getBuilder()->setBaseParam($baseParam)->insert()->values($dirtyData)->execute();
            return $ret;
        }
        else
        {
            $builder=static::getBuilder()->setBaseParam($baseParam)->update();
            if( !empty($dirtyData) ){
                $builder->set($dirtyData);
            }
            else if( !empty($this->increments) ){
                $builder->setIncrements($this->increments);
            }

            $ret=$builder->where($this->getIdQuery())->execute();
            return $ret;
        }
    }

    /**
     * @inheritDoc
     */
    public function delete()
    {
        $baseParam=[
            'id'=>$this->getId(),
            'routing'=>$this->getRouting()
        ];
        $ret=self::getBuilder()->setBaseParam($baseParam)->delete()->where($this->getIdQuery())->execute();
        return $ret;
    }

    public function getRouting()
    {
        $routing=current(Utility::arrayPick($this->getData(),[ static::$routingKey]));
        return $routing;
    }

    public function getId()
    {
        $id=implode('_', $this->getKey());
        return $id;
    }
}
