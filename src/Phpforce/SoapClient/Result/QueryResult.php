<?php

namespace Phpforce\SoapClient\Result;

/**
 * Query result
 */
class QueryResult
{
    protected $done;
    protected $queryLocator;
    protected $records = array();
    protected $size;

    /**
     * @return boolean
     */
    public function isDone()
    {
        return $this->done;
    }

    /**
     * @return string
     */
    public function getQueryLocator()
    {
        return $this->queryLocator;
    }

    /**
     * @return array
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    public function getRecord($index)
    {
        if (isset($this->records[$index])) {
            return $this->records[$index];
        }
    }
}