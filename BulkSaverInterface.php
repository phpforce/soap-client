<?php

namespace Ddeboer\Salesforce\ClientBundle;

interface BulkSaverInterface
{
    function save($object, $objectType);
    function delete($record);
    function flush();
}