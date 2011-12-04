<?php

namespace Ddeboer\Salesforce\ClientBundle\Tests\Response;

use Ddeboer\Salesforce\ClientBundle\Response\RecordIterator;
use Ddeboer\Salesforce\ClientBundle\Response\QueryResult;

class RecordIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIterateWithoutDomainModelSpecified()
    {
        $result = $this->getSalesforceResult();
        $recordIterator = new RecordIterator($this->getSoapClientMock(), $result);
        
        $this->assertEquals(3, $recordIterator->count());

        $i = 0;
        foreach ($recordIterator as $record) {
            $i++;
            $this->assertEquals("Record $i", $record->Name);
        }
        
        $this->assertEquals(3, $i);
    }
    
    public function testIterateQueryMore()
    {
        $result2 = $this->getSalesforceResult();
        $result2->records = array(
            (object) array(
                'Name'  => 'Record 4'
            ),
            (object) array(
                'Name'  => 'Record 5'
            ),
            (object) array(
                'Name'  => 'Record 6'
            )
        );
        
        $soapClientMock = $this->getSoapClientMock();
        $soapClientMock->expects($this->once())
                       ->method('queryMore')
                       ->will($this->returnValue($result2));
        
        $result = $this->getSalesforceResult();
        $result->done = false;
        $result->size = 6;
        
        $resultIterator = new RecordIterator($soapClientMock, $result);

        $this->assertEquals(6, $resultIterator->count());
        
        $i = 0;
        foreach ($resultIterator as $record) {
            $i++;
            $this->assertEquals("Record $i", $record->Name);
        }
        
        $this->assertEquals(6, $i);
    }
    
    public function testSort()
    {
        $result = $this->getSalesforceResult();
        $resultIterator = new RecordIterator($this->getSoapClientMock(), $result);
        
        $arrayIterator = $resultIterator->sort('Sort');
        $this->assertInstanceOf('ArrayIterator', $arrayIterator);
        
        $copy = $arrayIterator->getArrayCopy();
        $this->assertEquals('Record 3', $copy[0]->Name);
        $this->assertEquals('Record 1', $copy[1]->Name);
        $this->assertEquals('Record 2', $copy[2]->Name);
    }
    
    protected function getSoapClientMock()
    {
        return $this->getMockBuilder('\Ddeboer\Salesforce\ClientBundle\Client')
            ->disableOriginalConstructor()
            ->getMock();
    }
    
    /**
     * Mock result as it is returned from the Salesforce client
     * 
     * @return QueryResult
     */
    protected function getSalesforceResult()
    {
        $result = new QueryResult();
        $result->done = true;
        $result->size = 3;
        $result->queryLocator = null;
        $result->records = array(
            (object) array(
                'Name'  => 'Record 1',
                'Sort'  => 'Z'
            ),
            (object) array(
                'Name'  => 'Record 2',
                'Sort'  => ''
            ),
            (object) array(
                'Name'  => 'Record 3',
                'Sort'  => 'M'
            )
        );
        
        return $result;
    }
}