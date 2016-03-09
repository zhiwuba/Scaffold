<?php
/*
* This file is part of the Scaffold package.
*
* (c) bingxia liu  <xiabingliu@163.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Scaffold\Database\Query\ElasticSearch;


class Query implements ClauseInterface
{
    use ClauseTrait;

    protected $container;


    /**
     * @return mixed
     */
    public function toArray()
    {
        // TODO: Implement toArray() method.
    }
}