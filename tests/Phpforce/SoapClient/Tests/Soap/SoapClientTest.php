<?php
namespace Phpforce\SoapClient\Tests\Soap;

use Phpforce\SoapClient\Soap\SoapConnection;

class SoapClientTest extends \PHPUnit_Framework_TestCase
{
    public function testGetNamespacePartner()
    {
        $this->assertEquals('urn:partner.soap.sforce.com', $this->getSoapClientPartner()->getWsdl()->getTns());
    }
    
    protected function getSoapClientPartner()
    {
        return new SoapConnection(__DIR__.'/../Fixtures/sandbox.partner.wsdl.xml');
    }

    public function testGetNamespaceEnterprise()
    {
        $this->assertEquals('urn:enterprise.soap.sforce.com', $this->getSoapClientEnterprise()->getWsdl()->getTns());
    }

    protected function getSoapClientEnterprise()
    {
        return new SoapConnection(__DIR__.'/../Fixtures/sandbox.enterprise.wsdl.xml');
    }
}
