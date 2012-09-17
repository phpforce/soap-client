<?php

namespace Ddeboer\Salesforce\ClientBundle\Request;

class LeadConvert
{
    public $accountId;
    public $contactId;
    public $convertedStatus;
    public $doNotCreateOpportunity;
    public $leadId;
    public $opportunityName;
    public $overwriteLeadSource;
    public $ownerId;
    public $sendNotificationEmail;
}