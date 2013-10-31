<?php
namespace Phpforce\SoapClient;

use Doctrine\Common\Cache\ArrayCache;
use Psr\Log\LoggerInterface;
use Doctrine\Common\Cache\Cache;
use Phpforce\SoapClient\Soap\SoapConnectionFactory;
use Phpforce\SoapClient\Soap\WSDL\Wsdl;
use Phpforce\SoapClient\Plugin\LogPlugin;

/**
 * Salesforce SOAP client builder
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class ClientBuilder
{
    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var string
     */
    private $username, $password, $token;

    /**
     * @var Soap\WSDL\Wsdl
     */
    private $wsdl;

    /**
     * Construct client builder with required parameters
     *
     * @param Wsdl   $wsdl              Path to your Salesforce WSDL
     * @param string $username          Your Salesforce username
     * @param string $password          Your Salesforce password
     * @param string $token             Your Salesforce security token
     */
    public function __construct(Wsdl $wsdl, $username, $password, $token)
    {
        $this->wsdl = $wsdl;
        $this->username = $username;
        $this->password = $password;
        $this->token = $token;
    }

    /**
     * @param Cache $cache
     * @return $this
     */
    public function withCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Enable logging
     *
     * @param LoggerInterface $log Logger
     *
     * @return ClientBuilder
     */
    public function withLog(LoggerInterface $log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Build the Salesforce SOAP client
     *
     * @return ClientInterface
     */
    public function build()
    {
        $connectionFactory = new SoapConnectionFactory($this->cache);

        $connection = $connectionFactory->getInstance($this->wsdl);

        if($this->wsdl->getTns() === Wsdl::TNS_ENTERPRISE)
        {
            $client = new EnterpriseClient($connection, $this->username, $this->password, $this->token);
        }
        elseif($this->wsdl->getTns() === Wsdl::TNS_PARTNER)
        {
            $client = new PartnerClient($connection, $this->username, $this->password, $this->token);
        }
        else
        {
            throw new \UnexpectedValueException(sprintf('Wsdl with target namespace "%s" not supported.', $this->wsdl->getTns()));
        }

        if ($this->log)
        {
            $logPlugin = new LogPlugin($this->log);

            $client->getEventDispatcher()->addSubscriber($logPlugin);
        }

        return $client;
    }
}

