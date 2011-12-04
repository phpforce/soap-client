<?php

namespace Ddeboer\Salesforce\ClientBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Ddeboer\Salesforce\ClientBundle\Response\Error;

class ErrorEvent extends Event
{
    /**
     * @var Error
     */
    private $error;

    /**
     * Construct error event
     * 
     * @param Error $error
     */
    public function __construct(Error $error)
    {
        $this->error = $error;
    }

    /**
     * Get error
     *
     * @return Error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set error
     *
     * @param Error $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }
}