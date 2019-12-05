<?php
namespace Phpforce\SoapClient\Event;

use Symfony\Component\EventDispatcher\GenericEvent as Event;

class RequestEvent extends Event
{
    protected $method;
    protected $params = array();
    protected $response;

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

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }
}

