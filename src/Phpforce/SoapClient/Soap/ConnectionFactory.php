<?php
namespace Phpforce\SoapClient\Soap;

use Phpforce\SoapClient\Soap\TypeConverter;
use Phpforce\SoapClient\Soap\WSDL\Wsdl;

/**
 * Factory to create a \SoapClient properly configured for the Salesforce SOAP
 * client
 */
class ConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * Default classmap
     *
     * @var array
     */
    protected $classmap = array(
        'ChildRelationship'     => 'Phpforce\SoapClient\Result\ChildRelationship',
        'DeleteResult'          => 'Phpforce\SoapClient\Result\DeleteResult',
        'DeletedRecord'         => 'Phpforce\SoapClient\Result\DeletedRecord',
        'DescribeGlobalResult'  => 'Phpforce\SoapClient\Result\DescribeGlobalResult',
        'DescribeGlobalSObjectResult' => 'Phpforce\SoapClient\Result\DescribeGlobalSObjectResult',
        'DescribeSObjectResult' => 'Phpforce\SoapClient\Result\DescribeSObjectResult',
        'DescribeTab'           => 'Phpforce\SoapClient\Result\DescribeTab',
        'EmptyRecycleBinResult' => 'Phpforce\SoapClient\Result\EmptyRecycleBinResult',
        'Error'                 => 'Phpforce\SoapClient\Result\Error',
        'Field'                 => 'Phpforce\SoapClient\Result\DescribeSObjectResult\Field',
        'GetDeletedResult'      => 'Phpforce\SoapClient\Result\GetDeletedResult',
        'GetServerTimestampResult' => 'Phpforce\SoapClient\Result\GetServerTimestampResult',
        'GetUpdatedResult'      => 'Phpforce\SoapClient\Result\GetUpdatedResult',
        'GetUserInfoResult'     => 'Phpforce\SoapClient\Result\GetUserInfoResult',
        'LeadConvert'           => 'Phpforce\SoapClient\Request\LeadConvert',
        'LeadConvertResult'     => 'Phpforce\SoapClient\Result\LeadConvertResult',
        'LoginResult'           => 'Phpforce\SoapClient\Result\LoginResult',
        'MergeResult'           => 'Phpforce\SoapClient\Result\MergeResult',
        'QueryResult'           => 'Phpforce\SoapClient\Result\QueryResult',
        'SaveResult'            => 'Phpforce\SoapClient\Result\SaveResult',
        'SearchResult'          => 'Phpforce\SoapClient\Result\SearchResult',
        'SendEmailError'        => 'Phpforce\SoapClient\Result\SendEmailError',
        'SendEmailResult'       => 'Phpforce\SoapClient\Result\SendEmailResult',
        'SingleEmailMessage'    => 'Phpforce\SoapClient\Request\SingleEmailMessage',
        'sObject'               => 'Phpforce\SoapClient\Result\SObject',
        'UndeleteResult'        => 'Phpforce\SoapClient\Result\UndeleteResult',
        'UpsertResult'          => 'Phpforce\SoapClient\Result\UpsertResult',
    );

    /**
     * Type converters collection
     *
     * @var TypeConverter\TypeConverterCollection
     */
    protected $typeConverters;

    /**
     * {@inheritdoc}
     */
    public function getInstance(Wsdl $wsdl)
    {
        return new SoapConnection($wsdl, array(
            'trace'     => 1,
            'features'  => \SOAP_SINGLE_ELEMENT_ARRAYS,
            'classmap'  => $this->classmap,
            'typemap'   => $this->getTypeConverters()->getTypemap(),
            'cache_wsdl' => \WSDL_CACHE_MEMORY
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setClassmapping($soap, $php)
    {
        $this->classmap[$soap] = $php;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeConverters()
    {
        if (null === $this->typeConverters)
        {
            $this->typeConverters = new TypeConverter\TypeConverterCollection(array
            (
                new TypeConverter\DateTimeTypeConverter(),
                new TypeConverter\DateTypeConverter()
            ));
        }
        return $this->typeConverters;
    }

    /**
     * {@inheritdoc}
     */
    public function setTypeConverters(TypeConverter\TypeConverterCollection $typeConverters)
    {
        $this->typeConverters = $typeConverters;

        return $this;
    }
}