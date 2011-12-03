<?php

namespace Ddeboer\Salesforce\ClientBundle\Request;

class MergeRequest
{
    public $masterRecord;
    public $recordToMergeIds = array();
}