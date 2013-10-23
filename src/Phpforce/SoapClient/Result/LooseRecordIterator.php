<?php
namespace Phpforce\SoapClient\Result;

use Phpforce\SoapClient\Client;

/**
 * Iterator that contains records retrieved from the Salesforce API
 *
 * A maximum of 2000 records can be queried at once. If the end of those 2000
 * records is reached, an extra query to the Salesforce API will be issued to
 * fetch more records.
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class LooseRecordIterator extends RecordIterator
{
    /**
     * Get record at pointer, or, if there is no record, try to query Salesforce
     * for more records
     *
     * @param int $pointer
     *
     * @return object
     */
    protected function getObjectAt($pointer)
    {
        if (($current = $this->getQueryResult()->getRecord($pointer)))
        {
            $this->current = $current;

            foreach ($this->current as $key => &$value)
            {
                // PARTNER WSDL
                if($key === 'any')
                {
                    $this->client->cleanupAnyXML($this->current, $value);
                }
                elseif($key === 'Id' && is_array($value))
                {
                    $value = $value[0];
                }
            }
            return $this->current;
        }

        // If no record was found at pointer, see if there are more records
        // available for querying
        if (!$this->getQueryResult()->isDone())
        {
            $this->queryMore();

            return $this->getObjectAt($this->pointer);
        }
    }
}