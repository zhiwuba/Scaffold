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
    protected $builder;
    protected function setUp()
    {
        $this->builder=new \Scaffold\Database\Query\ElasticSearchBuilder('post');
        parent::setUp();
    }

    public function testSelect()
    {
        $ret=$this->builder->select()->andWhere('post_id', '=', 10)->andWhere('thread_id', '=', 20)->assemble();
        echo json_encode($ret, JSON_PRETTY_PRINT);
    }

    public function testSelect2()
    {
        $ret=$this->builder->select()->andWhere('post_id', '!=', 10)->andWhere('post_id', '!=', 20)->andWhere('thread_id', '=', 20)->assemble();
        echo json_encode($ret, JSON_PRETTY_PRINT);
    }

    public function testSelect3()
    {
        $ret=$this->builder->select()
        ->orWhere(function($query){
            $query->andWhere(function($query){
                $query->orWhere('post_id', '=', 10 )->orWhere('post_id', '=', 11);
            })->andWhere('thread_id', '=', 20);
        })->orWhere(function($query){
            $query->andWhere('post_id', '!=', 10)->andWhere('thread_id', '!=', 20);
        })->assemble();
        echo json_encode($ret, JSON_PRETTY_PRINT);
    }

}


$aa=<<<EOF
A & B

"bool":
{
  "must":
  [
    {
      "term": {
        "thread_id": "6204524624980034306"
      }
    },
    {
      "term": {
        "post_id": "6204701783791420161"
      }
    }
  ]
}
EOF;

$bb=<<<EOF
A & !B

"bool":
{
    "must":{
        "term":{"post_id": "6204701783791420161"}
    },
    "must_not":{
        "term": {"thread_id": "6204524624980034306"}
    }
}
EOF;

$cc=<<<EOF

(A or B) & (C or D)
"bool":
{
    "should":
    [
        {
            "bool":{
                "must":[
                    {"term":{"post_id": "6204701783791420161"}},
                    {"term":{"thread_id": "6204524624980034306"}}
                ]
            }
        },
        {
            "bool":{
                "must":[
                    {"term":{"post_id": "6204701783791420161"}},
                    {"term":{"thread_id": "6204524624980034306"}}
                ]
            }
        }
    ]
}

EOF;

$dd=<<<EOF
(A & B) or (C & D & !E)

"bool":
{
    "should":
    [
        {
            "bool":{
                "must":[
                    {"term":{"post_id": "6204701783791420161"}},
                    {"term":{"thread_id": "6204524624980034306"}}
                ]
            }
        },
        {
            "bool":{
                "must" : [{"term":{"":""}}, {"term":{"":""}}],
                "must_not": {"term":{  }}
            }
        }
    ]
}

->orWhere(
    $ this->andWhere(A)->andWhere(B)
)->orWhere(
    $ this->andWhere(C)->andWhere(D)->andWhere(!E)
);

EOF;


$ee=<<<EOF

->andWhere(
    this->orWhere()->orWhere()
)->andWhere(

)

   "filter": {
      "bool": {
         "must": [
            {
               "bool": {
                  "should": [
                     {
                        "term": {
                           "comments": "778"
                        }
                     },
                     {
                        "term": {
                           "comments": "5425"
                        }
                     }
                  ]
               }
            },
            {
                "term": {
                   "name": "peter"
                }
            }
         ]
      }
   }

EOF;
