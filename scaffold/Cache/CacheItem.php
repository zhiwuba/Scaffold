<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-25
 * Time: 下午9:58
 */


namespace Scaffold\Cache;
use Psr\Cache;

class CacheItem implements Cache\CacheItemInterface
{
    protected $key;
    protected $value;

    public function __construct($key,$value)
    {
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
        {
            return ;
        }
    }

    /**
     * @inheritDoc
     */
    public function isHit()
    {
        array_key_exists();
    }

    /**
     * @inheritDoc
     */
    public function set($value)
    {

    }

    /**
     * @inheritDoc
     */
    public function expiresAt($expiration)
    {

    }

    /**
     * @inheritDoc
     */
    public function expiresAfter($time)
    {

    }

}

