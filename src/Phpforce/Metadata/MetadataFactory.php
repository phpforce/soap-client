<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 24.10.13
 * Time: 15:23
 */

namespace Phpforce\Metadata;

use Phpforce\SoapClient\ClientInterface;

class MetadataFactory
{
    /**
     * @var Cache\CacheInterface
     */
    private $cache;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     * @param Cache $cache
     */
    public function __construct(ClientInterface $client, Cache\CacheInterface $cache)
    {
        $this->client = $client;

        $this->cache = $cache;
    }

    /**
     * @param $sobjectType
     */
    public function getMetadata($sobjectType)
    {
        $sobjectType = (array)$sobjectType;

        $retVal = array();

        $toFetch = array();

        foreach($sobjectType AS $type)
        {
            if(null === ($metadata = $this->cache->get($sobjectType)))
            {
                $toFetch[] = $type;
            }
            else
            {
                $retVal[] = $metadata;
            }
        }

        if(count($toFetch) > 0)
        {
            $metadata = $this->client->describeSObjects($toFetch);
        }

        foreach($metadata AS $metadatum)
        {
            $this->cache->set($metadatum);
            $retVal[] = $metadatum;
        }

        return $retVal;
    }


} 