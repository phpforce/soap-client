<?php

namespace Ddeboer\Salesforce\ClientBundle\Tests;

use Ddeboer\Salesforce\ClientBundle\Client;
use Ddeboer\Salesforce\ClientBundle\Request;
use Ddeboer\Salesforce\ClientBundle\Response;
use Ddeboer\Salesforce\ClientBundle\Event;
use Ddeboer\Salesforce\ClientBundle\Response\LoginResult;


class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testDelete()
    {
        $deleteResult = new Response\DeleteResult();
        $deleteResult->id = '001M0000008tWTFIA2';
        $deleteResult->success = true;

        $response = new \stdClass();
        $response->result = array($deleteResult);

        $soapClient = $this->getSoapClient(array('delete'));
        $soapClient->expects($this->once())
            ->method('delete')
            ->with(array('ids' => array('001M0000008tWTFIA2')))
            ->will($this->returnValue($response));

        $this->getClient($soapClient)->delete(array('001M0000008tWTFIA2'));

    }

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
    {;
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

    public function testMergeMustThrowException()
    {
        $soapClient= $this->getSoapClient(array('merge'));
        $this->setExpectedException('\InvalidArgumentException', 'must be an instance of');
        $this->getClient($soapClient)->merge(array(new \stdClass), 'Account');
    }

    public function testMerge()
    {
        $soapClient= $this->getSoapClient(array('merge'));

        $mergeRequest = new Request\MergeRequest();
        $masterRecord = new \stdClass();
        $masterRecord->Id = '001M0000007UvSjIAK';
        $masterRecord->Name = 'This will be the new name';
        $mergeRequest->masterRecord = $masterRecord;
        $mergeRequest->recordToMergeIds = array('001M0000008uw8JIAQ');

        $mergeResult= new Response\MergeResult();
        $mergeResult->id = '001M0000007UvSjIAK';
        $mergeResult->mergedRecordIds = array('001M0000008uw8JIAQ');
        $mergeResult->success = true;
        $result = new \stdClass();
        $result->result = array($mergeResult);

        $soapClient
            ->expects($this->any())
            ->method('merge')
            ->will($this->returnValue($result));

        $this->getClient($soapClient)->merge(array($mergeRequest), 'Account');
    }

    public function testWithEventDispatcher()
    {
        $response = new \stdClass();

        $error = new Response\Error();
        $error->fields = array('Id');
        $error->message = 'Account ID: id value of incorrect type: 001M0000008tWTFIA3';
        $error->statusCode = 'MALFORMED_ID';

        $saveResult = new Response\SaveResult();
        $saveResult->errors = array($error);
        $saveResult->success = false;

        $response->result = array($saveResult);

        $soapClient = $this->getSoapClient(array('create'));
        $soapClient
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($response));

        $client = $this->getClient($soapClient);

        $dispatcher = $this
            ->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->getMock();

        $c = new \stdClass();
        $c->AccountId = '123';

        $params = array(
            'sObjects'  => array(new \SoapVar($c, SOAP_ENC_OBJECT, 'Contact', Client::SOAP_NAMESPACE))
        );

        $dispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with('salesforce.client.request', new Event\RequestEvent('create', $params));

        $dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with('salesforce.client.response');

        $dispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with('salesforce.client.error');

        $this->setExpectedException('\InvalidArgumentException', 'MALFORMED_ID');

        $client->setEventDispatcher($dispatcher);
        $client->create(array($c), 'Contact');        
    }

    protected function getClient(\SoapClient $soapClient)
    {
        return new Client($soapClient, 'username', 'password', 'token');
    }

    protected function getSoapClient($methods)
    {
        $soapClient = $this->getMockBuilder('Ddeboer\Salesforce\ClientBundle\Soap\SoapClient')
            ->setMethods(array_merge($methods, array('login')))
            ->setConstructorArgs(array(__DIR__.'/Fixtures/sandbox.enterprise.wsdl.xml'))
            ->getMock();

        $loginResult = new \stdClass;
        $loginResult->result = new LoginResult;
        $loginResult->result->sessionId = '123';
        $loginResult->result->serverUrl = 'http://dinges';

        $soapClient
            ->expects($this->any())
            ->method('login')
            ->will($this->returnValue($loginResult));

        return $soapClient;
    }
}