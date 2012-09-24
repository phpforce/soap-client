<?php

namespace Phpforce\SoapClient\Result;

/**
 * A deleted record
 */
class DeletedRecord
{
    /**
     * @var \DateTime
     */
    protected $deletedDate;

    /**
     * @var string
     */
    protected $id;

    /**
     * Get deletion date
     *
     * @return \DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }

    /**
     * Get record id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}