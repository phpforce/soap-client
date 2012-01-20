<?php

namespace Ddeboer\Salesforce\ClientBundle\Response;

class LoginResult
{
    public $metadataServerUrl;
    public $passwordExpired;
    public $sandbox;
    public $serverUrl;
    public $sessionId;
    public $userId;
    public $userInfo;

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