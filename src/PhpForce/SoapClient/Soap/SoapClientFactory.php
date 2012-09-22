<?php
namespace PhpForce\SoapClient\Soap;

use PhpForce\SoapClient\Soap\TypeConverter;

/**
 * Factory to create a \SoapClient properly configured for the Salesforce SOAP
 * client
 */
class SoapClientFactory
{
    /**
     * Default classmap
     *
     * @var array
     */
    protected $classmap = array(
        'ChildRelationship'     => 'PhpForce\SoapClient\Result\ChildRelationship',
        'DeleteResult'          => 'PhpForce\SoapClient\Result\DeleteResult',
        'DeletedRecord'         => 'PhpForce\SoapClient\Result\DeletedRecord',
        'DescribeGlobalResult'  => 'PhpForce\SoapClient\Result\DescribeGlobalResult',
        'DescribeGlobalSObjectResult' => 'PhpForce\SoapClient\Result\DescribeGlobalSObjectResult',
        'DescribeSObjectResult' => 'PhpForce\SoapClient\Result\DescribeSObjectResult',
        'DescribeTab'           => 'PhpForce\SoapClient\Result\DescribeTab',
        'EmptyRecycleBinResult' => 'PhpForce\SoapClient\Result\EmptyRecycleBinResult',
        'Error'                 => 'PhpForce\SoapClient\Result\Error',
        'Field'                 => 'PhpForce\SoapClient\Result\DescribeSObjectResult\Field',
        'GetDeletedResult'      => 'PhpForce\SoapClient\Result\GetDeletedResult',
        'GetServerTimestampResult' => 'PhpForce\SoapClient\Result\GetServerTimestampResult',
        'GetUpdatedResult'      => 'PhpForce\SoapClient\Result\GetUpdatedResult',
        'GetUserInfoResult'     => 'PhpForce\SoapClient\Result\GetUserInfoResult',
        'LeadConvert'           => 'PhpForce\SoapClient\Request\LeadConvert',
        'LeadConvertResult'     => 'PhpForce\SoapClient\Result\LeadConvertResult',
        'LoginResult'           => 'PhpForce\SoapClient\Result\LoginResult',
        'MergeResult'           => 'PhpForce\SoapClient\Result\MergeResult',
        'QueryResult'           => 'PhpForce\SoapClient\Result\QueryResult',
        'SaveResult'            => 'PhpForce\SoapClient\Result\SaveResult',
        'SearchResult'          => 'PhpForce\SoapClient\Result\SearchResult',
        'SendEmailError'        => 'PhpForce\SoapClient\Result\SendEmailError',
        'SendEmailResult'       => 'PhpForce\SoapClient\Result\SendEmailResult',
        'sObject'               => 'PhpForce\SoapClient\Result\SObject',
        'UndeleteResult'        => 'PhpForce\SoapClient\Result\UndeleteResult'
    );

    /**
     * Type converters collection
     *
     * @var TypeConverterCollection
     */
    protected $typeConverters;

    /**
     * @param string $wsdl Some argument description
     *
     * @return void
     */
    public function factory($wsdl)
    {
        return new SoapClient($wsdl, array(
            'trace'     => 1,
            'features'  => \SOAP_SINGLE_ELEMENT_ARRAYS,
            'classmap'  => $this->classmap,
            'typemap'   => $this->getTypeConverters()->getTypemap()
        ));
    }

    /**
     * test
     *
     * @param string $soap SOAP class
     * @param string $php  PHP class
     */
    public function setClassmapping($soap, $php)
    {
        $this->classmap[$soap] = $php;
    }

    /**
     * Get type converter collection that will be used for the \SoapClient
     *
     * @return TypeConverterCollection
     */
    public function getTypeConverters()
    {
        if (null === $this->typeConverters) {
            $this->typeConverters = new TypeConverter\TypeConverterCollection(
                array(
                    new TypeConverter\DateTimeTypeConverter(),
                    new TypeConverter\DateTypeConverter()
                )
            );
        }

        return $this->typeConverters;
    }

    /**
     * Set type converter collection
     *
     * @param type $typeConverters Type converter collection
     *
     * @return SoapClientFactory
     */
    public function setTypeConverters(TypeConverter\TypeConverterCollection $typeConverters)
    {
        $this->typeConverters = $typeConverters;

        return $this;
    }
}