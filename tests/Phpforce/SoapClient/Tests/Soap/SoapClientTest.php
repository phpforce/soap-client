<?php
namespace Phpforce\SoapClient\Tests\Soap;

use PHPUnit\Framework\TestCase;

use Phpforce\SoapClient\Soap\SoapClient;

class SoapClientTest extends TestCase
{
    public function testGetNamespace()
    {
        $this->assertEquals('urn:partner.soap.sforce.com', $this->getSoapClient()->getNamespace('tns'));
    }
    
    protected function getSoapClient()
    {
        return new SoapClient(__DIR__.'/../Fixtures/sandbox.partner.wsdl.xml');
    }
}
