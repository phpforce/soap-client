<?php

namespace Phpforce\SoapClient\Result;

/**
 * Upsert result
 */
class UpsertResult extends SaveResult
{
    /**
     * @var boolean
     */
    protected $created;

    public function isCreated()
    {
        return $this->created;
    }
}