<?php

namespace Phpforce\SoapClient\Soap;

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
     * @param Wsdl  $wsdl
     * @param array $options
     */
    public function __construct(Wsdl $wsdl, array $options = array())
    {
        parent::__construct($wsdl->getPathname(), $options);

        $this->wsdl = $wsdl;
    }

    /**
     * @return Wsdl
     */
    public function getWsdl()
    {
        return $this->wsdl;
    }
}