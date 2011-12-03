<?php

namespace Ddeboer\Salesforce\ClientBundle\EventListener;

use Ddeboer\Salesforce\ClientBundle\Event;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class LogTransactionListener
{
    private $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onSalesforceClientResponse(Event\ResponseEvent $event)
    {
        $this->logger->debug('[Salesforce] response:', array($event->getResponse()));
    }

    public function onSalesforceClientError(Event\SoapFaultEvent $event)
    {
        $this->logger->err('[Salesforce] error: ' . $event->getSoapFault()->getMessage());
    }
}