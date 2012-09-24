<?php

namespace Phpforce\SoapClient\Result;

/**
 * Login result
 */
class LoginResult
{
    protected $metadataServerUrl;
    protected $passwordExpired;
    protected $sandbox;
    protected $serverUrl;
    protected $sessionId;
    protected $userId;
    protected $userInfo;

    /**
     * @return string
     */
    public function getMetadataServerUrl()
    {
        return $this->metadataServerUrl;
    }

    /**
     * @return boolean
     */
    public function getPasswordExpired()
    {
        return $this->passwordExpired;
    }

    /**
     * @return boolean
     */
    public function getSandbox()
    {
        return $this->sandbox;
    }

    /**
     * @return string
     */
    public function getServerUrl()
    {
        return $this->serverUrl;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return GetUserInfoResult
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * Get the server instance, e.g. ‘eu1-api’
     *
     * @return string
     */
    public function getServerInstance()
    {
        if (null === $this->serverUrl) {
            throw new \UnexpectedValueException('Server URL must be set');
        }

        $url = parse_url($this->serverUrl);
        $host = explode('.', $url['host']);

        return $host[0];
    }
}