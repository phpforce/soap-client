<?php

namespace Phpforce\SoapClient;

use Phpforce\SoapClient\Response\SaveResult;

interface BulkSaverInterface
{
    /**
     * Save a record in bulk
     *
     * @param object $record
     * @param string $objectType  The record type, e.g., Account
     * @param string $matchField  Optional match field for upserts
     * @return BulkSaver
     */
    function save($object, $objectType, $matchField = null);

    /**
     * Delete a record in bulk
     *
     * @param object $record  Any object is allowed, as long as it has an Id
     *                        property with non-empty value
     * @return BulkSaver
     */
    function delete($record);

    /**
     * Flush all creates, updates and upserts
     *
     * @return SaveResult[]
     */
    function flush();
}