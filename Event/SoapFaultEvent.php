<?php

namespace Ddeboer\Salesforce\ClientBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class SoapFaultEvent extends Event
{
    private $soapFault;

    public function __construct(\SoapFault $soapFault)
    {
        $this->soapFault = $soapFault;
    }

    public function getSoapFault()
    {
        return $this->soapFault;
    }

    public function setSoapFault($soapFault)
    {
        $this->soapFault = $soapFault;
    }
}