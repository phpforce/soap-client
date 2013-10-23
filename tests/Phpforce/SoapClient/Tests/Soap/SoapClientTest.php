<?php
namespace Phpforce\SoapClient\Tests\Soap;

use Phpforce\SoapClient\Soap\SoapClient;

class SoapClientTest extends \PHPUnit_Framework_TestCase
{
    public function testGetNamespacePartner()
    {
        $this->assertEquals('urn:partner.soap.sforce.com', $this->getSoapClientPartner()->getNamespace('tns'));
    }
    
    protected function getSoapClientPartner()
    {
        return new SoapClient(__DIR__.'/../Fixtures/sandbox.partner.wsdl.xml');
    }

    public function testGetNamespaceEnterprise()
    {
        $this->assertEquals('urn:enterprise.soap.sforce.com', $this->getSoapClientEnterprise()->getNamespace('tns'));
    }

    protected function getSoapClientEnterprise()
    {
        return new SoapClient(__DIR__.'/../Fixtures/sandbox.enterprise.wsdl.xml');
    }
}
