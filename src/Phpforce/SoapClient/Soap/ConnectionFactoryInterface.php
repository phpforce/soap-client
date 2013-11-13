<?php
namespace Phpforce\SoapClient\Soap;

use Phpforce\SoapClient\Soap\WSDL\Wsdl;
use Phpforce\SoapClient\Soap\TypeConverter;

/**
 * Factory to create a \SoapClient properly configured for the Salesforce SOAP
 * client
 */
interface ConnectionFactoryInterface
{
    /**
     * @param Wsdl $wsdl
     *
     * @return SoapConnection
     */
    public function getInstance(Wsdl $wsdl);

    /**
     * test
     *
     * @param string $soap SOAP class
     * @param string $php  PHP class
     */
    public function setClassmapping($soap, $php);

    /**
     * Get type converter collection that will be used for the \SoapClient
     *
     * @return TypeConverter\TypeConverterCollection
     */
    public function getTypeConverters();

    /**
     * Set type converter collection
     *
     * @param TypeConverter\TypeConverterCollection $typeConverters Type converter collection
     *
     * @return ConnectionFactoryInterface
     */
    public function setTypeConverters(TypeConverter\TypeConverterCollection $typeConverters);
}