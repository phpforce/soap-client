<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 24.10.13
 * Time: 15:26
 */

namespace Phpforce\Metadata\Cache;

use \Memcache AS MC;
use Phpforce\SoapClient\Result\DescribeSObjectResult;

class ApcCache implements CacheInterface
{
    /**
     * @var int
     */
    private $ttl;

    /**
     * @param int $ttl
     */
    public function __construct($ttl = 3600)
    {
        $this->ttl = $ttl;
    }

    /**
     * @param $soql
     * @return DescribeSObjectResult
     */
    public function get($sobjectType)
    {
        if( ! apc_exists($sobjectType))
        {
            return null;
        }
        return apc_fetch($sobjectType);
    }

    /**
     * @param $soql
     * @param Node $node
     * @return void
     */
    public function set(DescribeSObjectResult $result)
    {
        apc_store($result->getName(), $result, $this->ttl);
    }
}