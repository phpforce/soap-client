<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 23.10.13
 * Time: 12:40
 */

namespace Phpforce\SoapClient\Soap\WSDL;


class Wsdl implements \Serializable
{
    const TNS_ENTERPRISE    = 'urn:enterprise.soap.sforce.com';
    const TNS_PARTNER       = 'urn:partner.soap.sforce.com';

    /**
     * @var string
     */
    private $tns;

    /**
     * @var string[]
     */
    private $namespaces;

    /**
     * @var string
     */
    private $wsdl;

    /**
     * @param string $wsdl
     */
    public function __construct($wsdl)
    {
        $this->wsdl = $wsdl;

        $xml = new \SimpleXMLElement(\file_get_contents($wsdl));

        $this->namespaces = $xml->getDocNamespaces();

        $this->tns = isset($xml['targetNamespace']) ? (string)$xml['targetNamespace'] : null;
    }

    /**
     * @return array
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * @param string $ns
     * @return string $uri
     */
    public function getNamespace($ns = '')
    {
        return isset($this->namespaces[$ns]) ? $this->namespaces[$ns] : null;
    }

    /**
     * @return string
     */
    public function getTns()
    {
        return $this->tns;
    }

    /**
     * @return string
     */
    public function getPathname()
    {
        return $this->wsdl;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        serialize(array(
            'namespaces' => $this->namespaces,
            'tns' => $this->tns,
            'wsdl' => $this->wsdl
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $unserialized = unserialize($serialized);

        $this->namespaces    = $unserialized['namespaces'];
        $this->tns           = $unserialized['tns'];
        $this->wsdl          = $unserialized['wsdl'];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->wsdl;
    }
} 