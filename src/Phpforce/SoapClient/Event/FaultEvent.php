<?php
namespace Phpforce\SoapClient\Event;

use Symfony\Component\EventDispatcher\Event;

class FaultEvent extends Event
{
    /**
     * @var \SoapFault
     */
    protected $soapFault;

    /**
     * @var RequestEvent
     */
    protected $requestEvent;

    /**
     * @param \SoapFault $soapFault
     * @param RequestEvent $requestEvent
     */
    public function __construct(\SoapFault $soapFault, RequestEvent $requestEvent)
    {
        $this->setSoapFault($soapFault);
        $this->setRequestEvent($requestEvent);
    }

    /**
     * @return \SoapFault
     */
    public function getSoapFault()
    {
        return $this->soapFault;
    }

    /**
     * @param \SoapFault $soapFault
     */
    public function setSoapFault(\SoapFault $soapFault)
    {
        $this->soapFault = $soapFault;
    }

    /**
     * @return RequestEvent
     */
    public function getRequestEvent()
    {
        return $this->requestEvent;
    }

    /**
     * @param RequestEvent $requestEvent
     */
    public function setRequestEvent(RequestEvent $requestEvent)
    {
        $this->requestEvent = $requestEvent;
    }
}