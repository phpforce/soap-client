<?php

namespace Ddeboer\Salesforce\ClientBundle\Tests\Soap\Converter;

use Ddeboer\Salesforce\ClientBundle\Soap\Converter\DateTimeTypeConverter;

class DateTimeTypeConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertXmlToPhp()
    {
        $converter = new DateTimeTypeConverter();
        $result = $converter->convertXmlToPhp('<sf:SystemModstamp xmlns:sf="urn:sobject.enterprise.soap.sforce.com">2011-12-08T16:49:56.000Z</sf:SystemModstamp>');
        $this->assertEquals(new \DateTime('2011-12-08 17:49:56'), $result);
    }
}