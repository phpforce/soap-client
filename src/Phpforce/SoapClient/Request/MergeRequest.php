<?php

namespace Phpforce\SoapClient\Request;

class MergeRequest
{
    public $masterRecord;
    public $recordToMergeIds = array();
}