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

use Scaffold\Database\Query\CassandraBuilder;
use Test\TestCase;
use Scaffold\Database\Query\ElasticSearch\Filter;
use Scaffold\Database\Query\ElasticSearch\Body;
use Scaffold\Database\Query\ElasticSearch\Must;
use Scaffold\Database\Query\ElasticSearch\Term;
use Scaffold\Database\Query\ElasticSearch\Should;
use Scaffold\Database\Query\ElasticSearch\Boolean;


class ElasticSearchProtocol extends TestCase
{
    public function testFilter()
    {
        $body=Body::O()->addFilter(
            Filter::O()->addBool(
                Boolean::O()->addMust(
                    Must::O()->addTerm(
                        Term::O()->term('k', 'v')
                    )->addTerm(
                        Term::O()->exists('e')
                    )
                )->addShould(
                    Should::O()->addTerm(
                        Term::O()->terms('k', ['v1', 'v2', 'v3'])
                    )
                )
            )
        );

        echo json_encode($body->toArray(), JSON_PRETTY_PRINT);
    }
}
