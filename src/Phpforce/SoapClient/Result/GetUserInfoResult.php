<?php

namespace Phpforce\SoapClient\Result;

/**
 * User info result
 */
class GetUserInfoResult
{
    protected $accessibilityMode;

    protected $currencySymbol;

    protected $orgAttachmentFileSizeLimit;

    protected $orgDisallowHtmlAttachments;

    protected $orgHasPersonAccounts;

    protected $organizationId;

    protected $organizationMultiCurrency;

    protected $organizationName;

    protected $profileId;

    protected $roleId;

    protected $sessionSecondsValid;

    protected $userDefaultCurrencyIsoCode;

    protected $userEmail;

    protected $userFullName;

    protected $userId;

    protected $userLanguage;

    protected $userLocale;

    protected $userName;

    protected $userTimeZone;

    protected $userType;

    protected $userUiSkin;

    protected $orgDefaultCurrencyIsoCode;

    /**
     * @return boolean
     */
    public function getAccessibilityMode()
    {
        return $this->accessibilityMode;
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->currencySymbol;
    }

    /**
     * @return int
     */
    public function getOrgAttachmentFileSizeLimit()
    {
        return $this->orgAttachmentFileSizeLimit;
    }

    /**
     * @return boolean
     */
    public function getOrgDisallowHtmlAttachments()
    {
        return $this->orgDisallowHtmlAttachments;
    }

    /**
     * @return boolean
     */
    public function getOrgHasPersonAccounts()
    {
        return $this->orgHasPersonAccounts;
    }

    /**
     * @return string
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @return boolean
     */
    public function getOrganizationMultiCurrency()
    {
        return $this->organizationMultiCurrency;
    }

    /**
     * @return string
     */
    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    /**
     * @return string
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @return string
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * @return string
     */
    public function getSessionSecondsValid()
    {
        return $this->sessionSecondsValid;
    }

    /**
     * @return string
     */
    public function getUserDefaultCurrencyIsoCode()
    {
        return $this->userDefaultCurrencyIsoCode;
    }

    /**
     * @return string
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * @return string
     */
    public function getUserFullName()
    {
        return $this->userFullName;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getUserLanguage()
    {
        return $this->userLanguage;
    }

    /**
     * @return string
     */
    public function getUserLocale()
    {
        return $this->userLocale;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getUserTimeZone()
    {
        return $this->userTimeZone;
    }

    /**
     * @return string
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * @return string
     */
    public function getUserUiSkin()
    {
        return $this->userUiSkin;
    }

    /**
     * @return string
     */
    public function getOrgDefaultCurrencyIsoCode()
    {
        return $this->orgDefaultCurrencyIsoCode;
    }
}