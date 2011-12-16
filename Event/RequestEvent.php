<?php

namespace Ddeboer\Salesforce\ClientBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class RequestEvent extends Event
{
    private $method;
    private $params = array();

    public function __construct($method, array $params = array())
    {
        $this->setMethod($method);
        $this->setParams($params);
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setParams(array $params)
    {
        $this->params = $params;
    }
}