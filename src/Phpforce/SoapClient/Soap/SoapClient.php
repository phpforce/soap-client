<?php

namespace Phpforce\SoapClient\Soap;

/**
 * SOAP client used for the Salesforce API client
 *
 */
class SoapClient extends \SoapClient
{
    /**
     * SOAP types derived from WSDL
     *
     * @var array
     */
    protected $types;

    /**
     * Retrieve SOAP types from the WSDL and parse them
     *
     * @return array    Array of types and their properties
     */
    public function getSoapTypes()
    {
        if (null === $this->types) {

            $soapTypes = $this->__getTypes();
            foreach ($soapTypes as $soapType) {
                $properties = array();
                $lines = explode("\n", $soapType);
                if (!preg_match('/struct (.*) {/', $lines[0], $matches)) {
                    continue;
                }
                $typeName = $matches[1];

                foreach (array_slice($lines, 1) as $line) {
                    if ($line == '}') {
                        continue;
                    }
                    preg_match('/\s* (.*) (.*);/', $line, $matches);
                    $properties[$matches[2]] = $matches[1];
                }

                // Since every object extends sObject, need to append sObject elements to all native and custom objects
                if ($typeName !== 'sObject' && array_key_exists('sObject', $this->types)) {
                    $properties = array_merge($properties, $this->types['sObject']);
                }

                if ($typeName == 'SingleEmailMessage' && array_key_exists('Email', $this->types)) {
                    $properties = array_merge($properties, $this->types['Email']);
                }

                $this->types[$typeName] = $properties;
            }
        }

        return $this->types;
    }

    /**
     * Get a SOAP type’s elements
     *
     * @param string $type Object name
     * @return array       Elements for the type
     */

    /**
     * Get SOAP elements for a complexType
     *
     * @param string $complexType Name of SOAP complexType
     *
     * @return array  Names of elements and their types
     */
    public function getSoapElements($complexType)
    {
        $types = $this->getSoapTypes();
        if (isset($types[$complexType])) {
            return $types[$complexType];
        }
    }

    /**
     * Get a SOAP type’s element
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
}