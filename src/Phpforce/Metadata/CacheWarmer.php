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
     * @param ClientInterface $client
     * @param Cache $cache
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Fills the cache with all sobject describe
     * data for each (custom) object available at
     * the organization.
     */
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
                    $this->client->getConnection()->getCache()->save($sobjectDescribe->getName(), $sobjectDescribe);
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