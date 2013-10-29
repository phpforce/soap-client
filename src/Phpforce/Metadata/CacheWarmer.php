<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 24.10.13
 * Time: 17:00
 */

namespace Phpforce\Metadata;

use Doctrine\Common\Cache\Cache;
use Phpforce\SoapClient\ClientInterface;

class CacheWarmer
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var int
     */
    private $ttl;

    /**
     * @param ClientInterface $client
     * @param Cache $cache
     * @param int $ttl
     */
    public function __construct(ClientInterface $client, Cache $cache, $ttl = 0)
    {
        $this->client = $client;

        $this->cache = $cache;

        $this->ttl = $ttl;
    }

    public function warmup()
    {
        $globalSobjectDescribes = $this->client->describeGlobal()->sobjects;

        $bulk = array();

        $n = 0;

        while(true)
        {
            $bulk[] = current($globalSobjectDescribes)->name;

            if($n === 99)
            {
                foreach($this->client->describeSObjects($bulk) AS $sobjectDescribe)
                {
                    $this->cache->save($sobjectDescribe->getName(), $sobjectDescribe, $this->ttl);
                }

                $bulk = array();
                $n = 0;
                continue;
            }

            $n ++;

            if(false === next($globalSobjectDescribes))
            {
                break;
            }
        }
    }
} 