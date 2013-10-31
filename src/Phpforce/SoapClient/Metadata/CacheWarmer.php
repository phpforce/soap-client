<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 24.10.13
 * Time: 17:00
 */

namespace Phpforce\SoapClient\Metadata;

use Doctrine\Common\Cache\Cache;
use Phpforce\SoapClient\ClientInterface;

class CacheWarmer
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var bool
     */
    private $force;

    /**
     * @param ClientInterface $client
     * @param Cache $cache
     */
    public function __construct(ClientInterface $client, $force = false)
    {
        $this->client = $client;

        $this->force = $force;
    }

    /**
     * Fills the cache with all sobject describe
     * data for each (custom) object available at
     * the organization.
     *
     * @return void
     */
    public function warmup()
    {
        if($this->force)
        {
            $this->client->getConnection()->getCache()->delete('__global_describe');
        }

        $globalSobjectDescribes = $this->client->describeGlobal()->sobjects;

        $bulk = array();

        $n = 0;

        while(true)
        {
            $currenttype = current($globalSobjectDescribes)->name;

            // DELETE EXISTING CACHE ENTRIES
            if($this->force)
            {
                $this->client->getConnection()->getCache()->delete($currenttype);
            }

            $bulk[] = $currenttype;

            if($n === 99)
            {
                // FILLS CACHE
                $this->client->describeSObjects($bulk);

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