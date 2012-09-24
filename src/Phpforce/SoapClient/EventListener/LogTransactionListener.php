<?php

namespace Phpforce\SoapClient\EventListener;

use Phpforce\SoapClient\Event;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class LogTransactionListener
{
    /**
     * Logger
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Whether logging is enabled
     *
     * @var boolean
     */
    private $logging;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onSalesforceClientResponse(Event\ResponseEvent $event)
    {
        if (true === $this->logging) {
            $this->logger->debug('[Salesforce] response:', array($event->getResponse()));
        }
    }

    public function onSalesforceClientSoapFault(Event\SoapFaultEvent $event)
    {
        $this->logger->err('[Salesforce] fault: ' . $event->getSoapFault()->getMessage());
    }

    public function onSalesforceClientError(Event\ErrorEvent $event)
    {
        $error = $event->getError();
        $this->logger->err('[Salesforce] error: ' . $error->statusCode . ' - '
                           . $error->message, get_object_vars($error));
    }

    public function setLogging($logging)
    {
        $this->logging = $logging;
    }

    public function getLogging()
    {
        return $this->logging;
    }
}