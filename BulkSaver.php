<?php

namespace Ddeboer\Salesforce\ClientBundle;

use Ddeboer\Salesforce\ClientBundle\Client;
use Ddeboer\Salesforce\ClientBundle\Response\SaveResult;

/**
 * Add creates, updates and upserts to the queue, and issue them in bulk to
 * the Salesforce API
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class BulkSaver implements BulkSaverInterface
{
    /**
     * Maximum number of records that may be updated or created in one call
     */
    const BULK_SAVE_LIMIT = 200;

    /**
     * Maximum number of records that may be deleted in one call
     */
    const BULK_DELETE_LIMIT = 200;

    /**
     * Salesforce client
     *
     * @var Client
     */
    private $client;

    private $bulkCreateRecords = array();
    private $bulkDeleteRecords = array();
    private $bulkUpdateRecords = array();
    private $bulkUpsertRecords = array();
    private $bulkUpsertMatchFields = array();

    /**
     * Construct bulk saver
     *
     * @param Client $client        Salesforce client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Save a record in bulk
     * 
     * @param mixed $record
     * @param string $objectType    The record type, e.g., Account
     * @param string $matchField    Optional match field for upserts
     * @return BulkSaver
     */
    public function save($record, $objectType, $matchField = null)
    {
        if ($matchField) {
            $this->addBulkUpsertRecord($record, $objectType, $matchField);
        } elseif (isset($record->Id) && null !== $record->Id) {
            $this->addBulkUpdateRecord($record, $objectType);
        } else {
            $this->addBulkCreateRecord($record, $objectType);
        }

        return $this;
    }

    /**
     * Delete a record in bulk
     * 
     * @param type $record      Any object is allowed, as long as it has an Id
     *                          property with non-empty value
     * @return BulkSaver
     */
    public function delete($record)
    {
        if (!isset($record->Id) || !$record->Id) {
            throw new \InvalidArgumentException(
                'Only records with an Id can be deleted'
            );
        }

        $this->addBulkDeleteRecord($record);

        return $this;
    }

    /**
     * Flush all creates, updates and upserts
     *
     * @return SaveResult[]
     */
    public function flush()
    {
        $results = array();

        foreach ($this->bulkCreateRecords as $type => $objects) {
            if (count($objects) > 0) {
                $results[] = $this->flushCreates($type);
            }
        }

        foreach ($this->bulkUpdateRecords as $type => $objects) {
            if (count($objects) > 0) {
                $results[] = $this->flushUpdates($type);
            }
        }

        foreach ($this->bulkUpsertRecords as $type => $objects) {
            if (count($objects) > 0) {
                $results[] = $this->flushUpserts($type);
            }
        }

        if (count($this->bulkDeleteRecords) > 0) {
            $results[] = $this->flushDeletes();
        }

        return $results;
    }

    /**
     * Add a record to the create queue
     *
     * @param sObject $sObject
     * @param type $objectType
     */
    private function addBulkCreateRecord($record, $objectType)
    {
        if (isset($this->bulkCreateRecords[$objectType])
            && count($this->bulkCreateRecords[$objectType]) == self::BULK_SAVE_LIMIT) {
            $this->flushCreates($objectType);
        }

        $this->bulkCreateRecords[$objectType][] = $record;
    }

    /**
     * Add a record id to the bulk delete queue
     *
     * (Delete calls
     *
     * @param string $id
     */
    private function addBulkDeleteRecord($record)
    {
        if (self::BULK_DELETE_LIMIT === count($this->bulkDeleteRecords)) {
            $this->flushDeletes();
        }

        $this->bulkDeleteRecords[] = $record;
    }

     /**
     * Add a record to the update queue
     *
     * @param sObject $sObject
     * @param string $objectType
     */
    private function addBulkUpdateRecord($sObject, $objectType)
    {
        if (isset($this->bulkUpdateRecords[$objectType])
            && count($this->bulkUpdateRecords[$objectType]) == self::BULK_SAVE_LIMIT) {
            $this->flushUpdates($objectType);
        }

        $this->bulkUpdateRecords[$objectType][] = $sObject;
    }

    /**
     * Add a record to the update queue
     *
     * @param sObject $sObject
     * @param string $objectType
     */
    private function addBulkUpsertRecord($sObject, $objectType, $matchField)
    {
        $this->bulkUpsertMatchFields[$objectType] = $matchField;

        if (isset($this->bulkUpsertRecords[$objectType])
            && count($this->bulkUpsertRecords[$objectType]) == self::BULK_SAVE_LIMIT) {
            $this->flushUpserts($objectType);
        }
        
        $this->bulkUpsertRecords[$objectType][] = $sObject;
    }

     /**
     * Flush creates
     *
     * @param string $objectType
     * @return SaveResult[]
     */
    private function flushCreates($objectType)
    {
        $result = $this->client->create($this->bulkCreateRecords[$objectType], $objectType);
        $this->bulkCreateRecords[$objectType] = array();

        return $result;
    }

    /**
     * Flush deletes
     *
     * @return SaveResult[]
     */
    private function flushDeletes()
    {
        $ids = array();
        foreach ($this->bulkDeleteRecords as $record) {
            $ids[] = $record->Id;
        }

        $result = $this->client->delete($ids);
        $this->bulkDeleteRecords = array();

        return $result;
    }

    /**
     * Flush updates
     *
     * @param string $objectType
     * @return SaveResult
     */
    private function flushUpdates($objectType)
    {
        $result = $this->client->update($this->bulkUpdateRecords[$objectType], $objectType);
        $this->bulkUpdateRecords[$objectType] = array();

        return $result;
    }

    /**
     * Flush upserts
     *
     * @param string $objectType
     * @return SaveResult[]
     */
    private function flushUpserts($objectType)
    {
        $result = $this->client->upsert(
            $this->bulkUpsertMatchFields[$objectType],
            $this->bulkUpsertRecords[$objectType],
            $objectType);
        $this->bulkUpsertRecords[$objectType] = array();
        
        return $result;
    }

    /**
     * Flush all remaining creates, updates and upserts on BulkSaver destruction
     *
     */
    public function __destruct()
    {
        $this->flush();
    }
}