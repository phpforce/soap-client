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
     * SOAP types derived from WSDL
     *
     * @var array
     */
    protected $types;

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
            $this->cache = new ArrayCache();
        }
        $this->cache = $cache;
    }

    /**
     * Retrieve SOAP types from the WSDL and parse them
     *
     * Will be removed in further versions in favor of sf
     * metadata retrieved from describe calls (because of
     * performance and memory consumption issues in huge
     * organizations)
     *
     * @deprecated
     *
     * @return array    Array of types and their properties
     */
    public function getSoapTypes()
    {
        if (null === $this->types) {

            $soapTypes = $this->__getTypes();
            foreach ($soapTypes as $soapType)
            {
                $lines = explode("\n", $soapType);
                if (!preg_match('/struct (.*) {/', $lines[0], $matches))
                {
                    continue;
                }
                $typeName = $matches[1];

                foreach (array_slice($lines, 1) as $line)
                {
                    if ($line == '}')
                    {
                        continue;
                    }

                    preg_match('/\s* (.*) (.*);/', $line, $matches);

                    $properties[$matches[2]] = $matches[1];
                }
                $this->types[$typeName] = $properties;
            }
        }
        return $this->types;
    }

    /**
     * Get SOAP elements for a complexType
     *
     * Will be removed in further versions in favor of sf
     * metadata retrieved from describe calls (because of
     * performance and memory consumption issues in huge
     * organizations)
     *
     * @deprecated
     *
     * @param string $complexType Name of SOAP complexType
     *
     * @return array  Names of elements and their types
     */
    public function getSoapElements($complexType)
    {
        $types = $this->getSoapTypes();

        if (isset($types[$complexType]))
        {
            return $types[$complexType];
        }
    }

    /**
     * Get a SOAP type’s element
     *
     * Will be removed in further versions in favor of sf
     * metadata retrieved from describe calls (because of
     * performance and memory consumption issues in huge
     * organizations)
     *
     * @deprecated
     *
     * @param string $complexType Name of SOAP complexType
     * @param string $element     Name of element belonging to SOAP complexType
     *
     * @return string
     */
    public function getSoapElementType($complexType, $element)
    {
        $elements = $this->getSoapElements($complexType);
        if ($elements && isset($elements[$element])) {
            return $elements[$element];
        }
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