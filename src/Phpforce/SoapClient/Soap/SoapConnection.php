<?php

namespace Phpforce\SoapClient\Soap;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Phpforce\SoapClient\Soap\WSDL\Wsdl;

/**
 * SOAP client used for the Salesforce API client
 *
 */
class SoapConnection extends \SoapClient
{
    /**
     * @var Wsdl
     */
    protected $wsdl;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @param Wsdl  $wsdl
     * @param array $options
     * @param Cache $cache
     */
    public function __construct(Wsdl $wsdl, array $options = array(), Cache $cache = null)
    {
        parent::__construct($wsdl->getPathname(), $options);

        $this->wsdl = $wsdl;

        if(null === $cache)
        {
            $cache = new ArrayCache();
        }
        $this->cache = $cache;
    }

    /**
     * @return Wsdl
     */
    public function getWsdl()
    {
        return $this->wsdl;
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }
}