<?php

namespace Ddeboer\Salesforce\ClientBundle;

class Events
{
    const clientRequest   = 'salesforce.client.request';
    const clientResponse  = 'salesforce.client.response';
    const clientSoapFault = 'salesforce.client.soap_fault';
    const clientError     = 'salesforce.client.error';
}