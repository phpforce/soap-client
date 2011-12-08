<?php

namespace Ddeboer\Salesforce\ClientBundle\Response;

class MergeResult extends SaveResult
{
    public $mergedRecordIds;
    public $updatedRecordIds;
}