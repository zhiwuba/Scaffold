<?php
/*
 * This file is part of the Scaffold package.
 *
 * (c) bingxia liu  <xiabingliu@163.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scaffold\Cache;
use Psr\Cache;
use Scaffold\Helper\Utility;

class CacheItem implements Cache\CacheItemInterface
{
    protected $isHit;
    protected $key;
    protected $value;
    protected $ttl=0;

    public function __construct($key,$value, $hit=false)
    {
        $this->isHit=$hit;
        $this->key=$key;
        $this->value=$value;
    }

    /**
     * @inheritDoc
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function get()
    {
        if( $this->isHit() )
            return $this->value;
        else
            return null;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function isHit()
    {
        return $this->isHit;
    }

    /**
     * @inheritDoc
     */
    public function set($value)
    {
        $this->value=$value;
        return $this; //todo return what?
    }

    /**
     * @inheritDoc
     */
    public function expiresAt($expiration)
    {
        if( $expiration instanceof \DateTime )
        {
            $now=new \DateTime();
            $second=Utility::dateTimeInterval($now, $expiration);
            $this->ttl=$second;
        }
        else if( $expiration ==='none' || $expiration===null)
        {   //permanent
            $this->ttl=$expiration;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter($time)
    {
        if( $time instanceof \DateInterval )
        {
            $sec=Utility::dateIntervalToSec($time);
            $this->ttl=$sec;
        }
        else if( is_integer($time) || $time===null || $time==='none' )
        {
            $this->ttl=$time;
        }
        return $this;
    }

}

