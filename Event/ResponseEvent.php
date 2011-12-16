<?php

namespace Ddeboer\Salesforce\ClientBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ResponseEvent extends Event
{
    private $requestEvent;
    private $response;

    public function __construct(RequestEvent $requestEvent, $response)
    {
        $this->setRequestEvent($requestEvent);
        $this->setResponse($response);
    }

    public function getRequestEvent()
    {
        return $this->requestEvent;
    }

    public function setRequestEvent($requestEvent)
    {
        $this->requestEvent = $requestEvent;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }
}