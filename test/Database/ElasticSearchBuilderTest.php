<?php
 /*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Database;

class ElasticSearchBuilderTest extends \Test\TestCase
{
    /**
     * @var  \Scaffold\Database\Query\ElasticSearchBuilder
     */
    protected $builder;

    protected function setUp()
    {
        $this->builder=new \Scaffold\Database\Query\ElasticSearchBuilder('paints');
        $this->builder->setBaseParam(['index'=>'gallery']);
        parent::setUp();
    }

    public function testSelect()
    {
        $ret=$this->builder->select()->andWhere('comments', '=', 8841)->andWhere('likes', '=', 2080)->fetchAll();
        echo json_encode($ret, JSON_PRETTY_PRINT);
    }

    public function testSelect2()
    {
        $ret=$this->builder->select()->andWhere('name', '!=', 'peter')->andWhere('author', '!=', 'indie')->andWhere('id', '=', 98)->fetchAll();
        echo json_encode($ret, JSON_PRETTY_PRINT);
    }

    public function testSelect3()
    {
        $ret=$this->builder->select()
            ->orWhere(function($query){
                $query->andWhere(function($query){
                    $query->orWhere('name', '=', 'elizabeth')->orWhere('name', '=', 'william');
                })->andWhere('id', '!=', 48);
            })->orWhere(function($query){
                $query->andWhere('comments', '=', 8841)->andWhere('likes', '=', 2080);
            })->fetchAll();

        echo json_encode($ret, JSON_PRETTY_PRINT);
    }

}
