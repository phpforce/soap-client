<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 24.10.13
 * Time: 15:26
 */

namespace Phpforce\Metadata\Cache;

use \Memcache AS MC;
use Phpforce\SoapClient\Result\DescribeGlobalResult;
use Phpforce\SoapClient\Result\DescribeSObjectResult;

class Memcache implements CacheInterface
{
    /**
     * @var \Memcache
     */
    private $mc;

    /**
     * @var int
     */
    private $ttl;

    /**
     * @param int $ttl
     */
    public function __construct($ttl = 3600, $port = 11211)
    {
        $this->ttl = $ttl;

        $this->mc = new MC;

        $this->mc->pconnect('127.0.0.1', 11211);
    }

    /**
     * @param $soql
     * @return DescribeSObjectResult
     */
    public function get($sobjectType)
    {
        if(false === ($metadata = $this->mc->get($sobjectType)))
        {
            return null;
        }
        return $metadata;
    }

    /**
     * @param $soql
     * @param Node $node
     * @return void
     */
    public function set(DescribeSObjectResult $result)
    {
        $this->mc->set($result->getName(), $result, MEMCACHE_COMPRESSED, $this->ttl);
    }
}