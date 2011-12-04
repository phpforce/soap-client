<?php

namespace Ddeboer\Salesforce\ClientBundle\Tests;

use Ddeboer\Salesforce\ClientBundle\Client;
use Ddeboer\Salesforce\ClientBundle\Response;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testQuery()
    {
        $soapClient = $this->getSoapClient(array('query'));
        $response = new \stdClass();
        $response->result = new Response\QueryResult();
        $response->result->size = 1;
        $response->result->done = true;
        $response->result->queryLocator = null;
        $response->result->records = array(
            (object) array(
                'Id'    => '001M0000008tWTFIA2',
                'Name'  => 'Company'
            )
        );

        $soapClient->expects($this->any())
                ->method('query')
                ->will($this->returnValue($response));

        $client = new Client($soapClient, 'username', 'password', 'token');
        $result = $client->query('Select Name from Account Limit 1');
        $this->assertInstanceOf('Ddeboer\Salesforce\ClientBundle\Response\RecordIterator', $result);
        $this->assertEquals(1, $result->count());
    }

    public function testInvalidQueryThrowsSoapFault()
    {
        $soapClient = $this->getSoapClient(array('query'));
        $soapClient
            ->expects($this->once())
            ->method('query')
            ->will($this->throwException(new \SoapFault('C', "INVALID_FIELD:
Select aId, Name from Account LIMIT 1
       ^
ERROR at Row:1:Column:8
No such column 'aId' on entity 'Account'. If you are attempting to use a custom field, be sure to append the '__c' after the custom field name. Please reference your WSDL or the describe call for the appropriate names.")));

        $client = $this->getClient($soapClient);

        $this->setExpectedException('\SoapFault');
        $client->query('Select NonExistingField from Account');
    }

    public function testInvalidUpdateResultsInError()
    {
        $error = new Response\Error();
        $error->fields = array('Id');
        $error->message = 'Account ID: id value of incorrect type: 001M0000008tWTFIA3';
        $error->statusCode = 'MALFORMED_ID';

        $saveResult = new Response\SaveResult();
        $saveResult->errors = array($error);
        $saveResult->success = false;

        $result = new \stdClass();
        $result->result = array($saveResult);

        $soapClient = $this->getSoapClient(array('update'));
        $soapClient
            ->expects($this->once())
            ->method('update')
            ->will($this->returnValue($result));

        $this->setExpectedException('\InvalidArgumentException', 'MALFORMED_ID');
        $this->getClient($soapClient)->update(array(
            (object) array(
                'Id'    => 'invalid-id',
                'Name'  => 'Some name'
            )
        ), 'Account');
    }

    protected function getClient(\SoapClient $soapClient)
    {
        return new Client($soapClient, 'username', 'password', 'token');
    }

    protected function getSoapClient($methods)
    {
        $soapClient = $this->getMockBuilder('\SoapClient')
            ->setMethods(array_merge($methods, array('login')))
            ->setConstructorArgs(array(__DIR__.'/Fixtures/sandbox.enterprise.wsdl.xml'))
            ->getMock();

        $loginResult = new \stdClass;
        $loginResult->result = new \stdClass;
        $loginResult->result->sessionId = '123';
        $loginResult->result->serverUrl = 'http://dinges';

        $soapClient
            ->expects($this->once())
            ->method('login')
            ->will($this->returnValue($loginResult));

        return $soapClient;
    }
}