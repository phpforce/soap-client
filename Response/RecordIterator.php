<?php

namespace Ddeboer\Salesforce\ClientBundle\Response;

use Ddeboer\Salesforce\ClientBundle\Client;
use Ddeboer\Salesforce\ClientBundle\Response\QueryResult;

/**
 * Iterator that contains records retrieved from the Salesforce API
 *
 * A maximum of 2000 records can be queried at once. If the end of those 2000
 * records is reached, an extra query to the Salesforce API will be issued to
 * fetch more records.
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class RecordIterator implements \SeekableIterator, \Countable
{
    /**
     * Salesforce client 
     *
     * @var Client
     */
    protected $client;

    /**
     * Query result
     * 
     * @var QueryResult
     */
    private $queryResult;

    /**
     * Iterator pointer
     *
     * @var int
     */
    protected $pointer = 0;

    /**
     * Cached current domain model object
     *
     * @var mixed
     */
    protected $current;

    /**
     * Construct a record iterator
     *
     * @param client $client
     * @param string $result
     */
    public function __construct(Client $client, QueryResult $result)
    {
        $this->client = $client;
        $this->setQueryResult($result);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Get record at pointer, or, if there is no record, try to query Salesforce
     * for more records
     *
     * @param int $pointer
     * @return mixed
     */
    protected function getObjectAt($pointer)
    {
        if (isset($this->queryResult->records[$pointer])) {
            $this->current = $this->queryResult->records[$pointer];
            return $this->current;
        }

        // If no record was found at pointer, see if there are more records
        // available for querying
        if (false === $this->queryResult->done) {
            $this->queryMore();
            return $this->getObjectAt($this->pointer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->pointer;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
            $this->pointer++;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->pointer = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return null != $this->getObjectAt($this->pointer);
    }

    /**
     * Get first object
     *
     * @return mixed
     */
    public function getFirst()
    {
        return $this->getObjectAt(0);
    }

    /**
     * Set query result, as it is returned from Salesforce
     *
     * @param QueryResult $result
     * @return RecordIterator
     */
    public function setQueryResult(QueryResult $result)
    {
        $this->queryResult = $result;
        
        return $this;
    }

    /**
     * Query Salesforce for more records and rewind iterator
     *
     */
    protected function queryMore()
    {
        $result = $this->client->queryMore($this->queryResult->queryLocator);
        $this->setQueryResult($result);
        $this->rewind();
    }

    /**
     * Get total number of records returned from Salesforce
     *
     * @return int
     */
    public function count()
    {
        return $this->getQueryResult()->size;
    }

    public function seek($position)
    {
        return $this->getObjectAt($position);
    }

    /**
     * Get sorted result iterator for the records on the current page
     *
     * Note: this method will not query Salesforce for records outside the
     * current page. If you wish to sort larger sets of Salesforce records, do
     * so in the select query you issue to the Salesforce API.
     *
     * @param string $by
     * @return \ArrayIterator
     */
    public function sort($by)
    {
        $by = ucfirst($by);
        $array = $this->records;
        usort($array, function($a, $b) use ($by) {
            // These two ifs take care of moving empty values to the end of the
            // array instead of the beginning
            if (!isset($a->$by) || !$a->$by) {
                return 1;
            }

            if (!isset($b->$by) || !$b->$by) {
                return -1;
            }
            return strcmp($a->$by, $b->$by);
        });

        return new \ArrayIterator($array);
    }

    public function getQueryResult()
    {
        return $this->queryResult;
    }
}