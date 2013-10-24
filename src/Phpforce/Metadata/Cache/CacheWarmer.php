<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 24.10.13
 * Time: 17:00
 */

namespace Phpforce\Metadata\Cache;

use Phpforce\SoapClient\ClientInterface;

class CacheWarmer
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     * @param CacheInterface $cache
     */
    public function __construct(ClientInterface $client, CacheInterface $cache)
    {
        $this->client = $client;

        $this->cache = $cache;
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
                    $this->cache->set($sobjectDescribe);
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