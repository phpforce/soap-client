<?php

namespace Phpforce\SoapClient\Tests;

use Phpforce\SoapClient\Client;
use Phpforce\SoapClient\Request;
use Phpforce\SoapClient\Result;
use Phpforce\SoapClient\Event;
use Phpforce\SoapClient\Result\LoginResult;
use \ReflectionClass;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testDelete()
    {
        $deleteResult = $this->createMock(new Result\DeleteResult(), array(
            'id' => '001M0000008tWTFIA2',
            'success' => true
        ));

        $result = new \stdClass;
        $result->result = array($deleteResult);

        $soapClient = $this->getSoapClient(array('delete'));
        $soapClient->expects($this->once())
            ->method('delete')
            ->with(array('ids' => array('001M0000008tWTFIA2')))
            ->will($this->returnValue($result));

        $this->getClient($soapClient)->delete(array('001M0000008tWTFIA2'));

    }

    public function testQuery()
    {
        $soapClient = $this->getSoapClient(array('query'));

        $result = $this->getResultMock(new Result\QueryResult, array(
            'size' => 1,
            'done' => true,
            'records' => array(
                (object) array(
                    'Id'    => '001M0000008tWTFIA2',
                    'Name'  => 'Company'
                )
            )
        ));

        $soapClient->expects($this->any())
                ->method('query')
                ->will($this->returnValue($result));

        $client = new Client($soapClient, 'username', 'password', 'token');
        $result = $client->query('Select Name from Account Limit 1');
        $this->assertInstanceOf('Phpforce\SoapClient\Result\RecordIterator', $result);
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
        $error = $this->createMock(new Result\Error(), array(
            'fields' => array('Id'),
            'message' => 'Account ID: id value of incorrect type: 001M0000008tWTFIA3',
            'statusCode' => 'MALFORMED_ID'
        ));

        $saveResult = $this->createMock(new Result\SaveResult(), array(
            'errors' => array($error),
            'success' => false
        ));

        $result = new \stdClass();
        $result->result = array($saveResult);

        $soapClient = $this->getSoapClient(array('update'));
        $soapClient
            ->expects($this->once())
            ->method('update')
            ->will($this->returnValue($result));

        $this->setExpectedException('\Phpforce\SoapClient\Exception\SaveException');
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

        $mergeResult = $this->createMock(new Result\MergeResult(), array(
            'id' => '001M0000007UvSjIAK',
            'mergedRecordIds' => array('001M0000008uw8JIAQ'),
            'success' => true
        ));

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

        $error = $this->createMock(new Result\Error(), array(
            'fields' => array('Id'),
            'message' => 'Account ID: id value of incorrect type: 001M0000008tWTFIA3',
            'statusCode' => 'MALFORMED_ID'
        ));

        $saveResult = $this->createMock(new Result\SaveResult(), array(
            'errors' => array($error),
            'success' => false
        ));

        $response->result = array($saveResult);

        $soapClient = $this->getSoapClient(array('create'));
        $soapClient
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($response));

        $client = $this->getClient($soapClient);

        $dispatcher = $this
            ->getMockBuilder('\Symfony\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->getMock();

        $c = new \stdClass();
        $c->AccountId = '123';

        $params = array(
            'sObjects'  => array(new \SoapVar($c, SOAP_ENC_OBJECT, 'Contact', Client::SOAP_NAMESPACE))
        );

//        $dispatcher
//            ->expects($this->at(0))
//            ->method('dispatch')
//            ->with('php_force.soap_client.request', new Event\RequestEvent('create', $params));

        $dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with('phpforce.soap_client.response');

//        $dispatcher
//            ->expects($this->at(2))
//            ->method('dispatch')
//            ->with('php_force.soap_client.error');

        $this->setExpectedException('\Phpforce\SoapClient\Exception\SaveException');

        $client->setEventDispatcher($dispatcher);
        $client->create(array($c), 'Contact');
    }

    protected function getClient(\SoapClient $soapClient)
    {
        return new Client($soapClient, 'username', 'password', 'token');
    }

    protected function getSoapClient($methods)
    {
        $soapClient = $this->getMockBuilder('Phpforce\SoapClient\Soap\SoapClient')
            ->setMethods(array_merge($methods, array('login')))
            ->setConstructorArgs(array(__DIR__.'/Fixtures/sandbox.enterprise.wsdl.xml'))
            ->getMock();

        $result = $this->getResultMock(new LoginResult(), array(
            'sessionId' => '123',
            'serverUrl' => 'http://dinges'
        ));

        $soapClient
            ->expects($this->any())
            ->method('login')
            ->will($this->returnValue($result));

        return $soapClient;
    }

    /**
     * Set a protected property on an object for testing purposes
     *
     * @param object $object   Object
     * @param string $property Property name
     * @param mixed  $value    Value
     */
    protected function setProperty($object, $property, $value)
    {
        $reflClass = new ReflectionClass($object);
        $reflProperty = $reflClass->getProperty($property);
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($object, $value);

        return $this;
    }

    protected function createMock($object, array $values = array())
    {
        foreach ($values as $key => $value) {
            $this->setProperty($object, $key, $value);
        }

        return $object;
    }

    protected function getResultMock($object, array $values = array())
    {
        $mock = $this->createMock($object, $values);

        $result = new \stdClass();
        $result->result = $mock;

        return $result;
    }
}
