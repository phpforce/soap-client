<?php
namespace Phpforce\SoapClient\Request;

class LeadConvert
{
    public $accountId;
    public $contactId;
    public $convertedStatus;
    public $doNotCreateOpportunity = false;
    public $leadId;
    public $opportunityName;
    public $overwriteLeadSource = false;
    public $ownerId;
    public $sendNotificationEmail = false;
}