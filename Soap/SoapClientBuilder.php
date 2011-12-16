<?php

namespace Ddeboer\Salesforce\ClientBundle\Soap;

use BeSimple\SoapBundle\Soap\SoapClientBuilder as BeSimpleSoapClientBuilder;

class SoapClientBuilder extends BeSimpleSoapClientBuilder
{
    public function build()
    {
        if (!$this->soapClient) {
             $this->validateOptions();

            return new SoapClient($this->wsdl, $this->getSoapOptions());
        }

        return $this->soapClient;
    }
}