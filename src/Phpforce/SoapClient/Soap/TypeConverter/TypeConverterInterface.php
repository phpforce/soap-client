<?php

namespace Phpforce\SoapClient\Soap\TypeConverter;

/**
 * A type converter converts between SOAP and PHP types
 */
interface TypeConverterInterface
{
    /**
     * Get type namespace.
     *
     * @return string
     */
    function getTypeNamespace();

    /**
     * Get type name.
     *
     * @return string
     */
    function getTypeName();

    /**
     * Convert given XML string to PHP type.
     *
     * @param string $xml XML string
     *
     * @return mixed
     */
    function convertXmlToPhp($xml);

    /**
     * Convert PHP type to XML string.
     *
     * @param mixed $php PHP type
     *
     * @return string
     */
    function convertPhpToXml($php);
}