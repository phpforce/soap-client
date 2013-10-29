<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 24.10.13
 * Time: 15:23
 */

namespace Phpforce\Metadata;

use Doctrine\Common\Cache\Cache;
use Phpforce\SoapClient\ClientInterface;
use Phpforce\SoapClient\Result\DescribeSObjectResult;

/**
 * Class MetadataFactory
 * @package Phpforce\Metadata
 */
class MetadataFactory
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var int $ttl: The metadata cache TTL
     */
    private $ttl;

    /**
     * @param ClientInterface $client
     * @param int $ttl
     */
    public function __construct(ClientInterface $client, $ttl = 0)
    {
        $this->client = $client;

        $this->ttl = $ttl;
    }

    /**
     * @param string $sobjectType
     * @return DescribeSObjectResult[]
     */
    public function describeSobjects($sobjectType)
    {
        $sobjectType = (array)$sobjectType;

        $retVal = array();

        $toFetch = array();

        foreach($sobjectType AS $type)
        {
            if($this->client->getConnection()->getCache()->contains($type))
            {
                $retVal[$type] = $this->client->getConnection()->getCache()->fetch($type);
            }
            else
            {
                $toFetch[] = $type;
            }
        }

        if(count($toFetch) > 0)
        {
            $metadata = $this->client->describeSObjects($toFetch);

            foreach($metadata AS $metadatum)
            {
                $this->client->getConnection()->getCache()->save($medadatum->getName(), $metadatum, $this->ttl);

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