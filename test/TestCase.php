<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test;

use Scaffold\Cache\Adapter\RedisAdapter;
use Scaffold\Database\Connector\CassandraConnector;
use Scaffold\Database\Connector\ElasticSearchConnector;
use Scaffold\Database\Connector\MysqlConnector;
use Scaffold\Database\Query\MysqlBuilder;
use Scaffold\Database\Query\CassandraBuilder;
use Scaffold\Database\Query\ElasticSearchBuilder;
use Scaffold\Cache\CacheItemPool;
use Test\Database\PaintModel;

class TestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $configs=include 'Config.php';

        $connector=new MysqlConnector($configs['mysql']);
        MysqlBuilder::setConnector($connector);

        //$connector=new CassandraConnector($configs['cassandra']);
        //CassandraBuilder::setConnection($connector->getConnection());

        $connector=new ElasticSearchConnector($configs['elasticsearch']);
        ElasticSearchBuilder::setConnection($connector->getConnection());

        $redis=new \Predis\Client();
        CacheItemPool::setAdapter(new RedisAdapter($redis));

        //$this->installESData();

        parent::setUp();
    }

    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        //$this->uninstallESData();
        parent::tearDown();
    }


    protected function installESData()
    {
        $elastic=ElasticSearchBuilder::getConnection();

        if( !$elastic->indices()->exists(['index'=>'gallery']) ) {
            $elastic->indices()->create([
                'index'=>'gallery',
                'body'=>PaintModel::$mapping,
            ]);
        }

        $name='john kim david Michael Lily William Peter Rachel Daniel Elizabeth';
        $author='Camila Eira Eleanora Ellen Emerson Estelle Everly Gaia Indie Ione Isobel Jocelyn Judith Kaia Kalila Liliana Lucille Marin Marley Meilani Mireille Norah Orla Paloma Pandora Peyton Polly';
        $mark='optimistic independent out-going active able adaptable active aggressive ambitious amiable amicable analytical apprehensive aspiring audacious capable careful candid competent constructive cooperative creative dedicated dependable diplomatic disciplined dutiful well--educated  efficient  energetic expressivity faithful frank generous genteel gentle humorous impartial independent industrious ingenious motivated intelligent learned logical methodical modest objective precise punctual realistic responsible sensible porting steady systematic purposeful sweet-tempered temperate tireless Personality';

        $names=explode(' ', $name);
        $authors=explode(' ', $author);
        $marks=explode(' ', $mark);

        for($i=1; $i<=3000;$i++)
        {
            shuffle($marks);

            $paint=new PaintModel();
            $paint['id']=$i;
            $paint['name']= $names[rand(0, count($names)-1)];
            $paint['filename']=$paint['name'] . '\'s file, code is ' . $i;
            $paint['author']=$authors[rand(0, count($authors)-1)];
            $paint['mark']= implode('' , array_slice($marks , 0, rand(1, count($marks))) );
            $paint['created_at']='2012-02-06';
            $paint['comments']=rand(0,10000);
            $paint['likes']=rand(0, 10000);
            $paint->save();
        }
    }

    protected function uninstallESData()
    {
        $elastic=ElasticSearchBuilder::getConnection();
        $elastic->indices()->delete(['index'=>'gallery']);
    }

}

