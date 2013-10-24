<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 24.10.13
 * Time: 15:23
 */

namespace Phpforce\Metadata;

use Phpforce\SoapClient\ClientInterface;
use Phpforce\SoapClient\Result\DescribeSObjectResult;

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
     * @param string $sobjectType
     * @return array<DescribeSObjectResult>
     */
    public function describeSobjects($sobjectType)
    {
        $sobjectType = (array)$sobjectType;

        $retVal = array();

        $toFetch = array();

        foreach($sobjectType AS $type)
        {
            if(null === ($metadatum = $this->cache->get($type)))
            {
                $toFetch[] = $type;
            }
            else
            {
                $retVal[$type] = $metadatum;
            }
        }

        if(count($toFetch) > 0)
        {
            $metadata = $this->client->describeSObjects($toFetch);

            foreach($metadata AS $metadatum)
            {
                $this->cache->set($metadatum);
                $retVal[$metadatum->getName()] = $metadatum;
            }
        }
        return $retVal;
    }

    /**
     * @param $sobjectType
     * @return DescribeSObjectResult
     */
    public function describeSobject($sobjectType)
    {
        $metadata = $this->describeSobjects($sobjectType);

        return $metadata[$sobjectType];
    }
}