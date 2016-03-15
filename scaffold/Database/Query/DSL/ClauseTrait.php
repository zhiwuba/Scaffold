<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scaffold\Database\Query\DSL;

trait ClauseTrait
{
    /**
     * @var ClauseInterface[]|string[]
     */
    protected $container=[];

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->container);
    }

    /**
     * @return static
     */
    public static function O()
    {
        return new static;
    }
}

