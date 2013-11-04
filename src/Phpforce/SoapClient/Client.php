<?php
namespace Phpforce\SoapClient;

use Codemitte\ForceToolkit\Soap\Mapping\Base\LoginResult;
use Doctrine\Common\Cache\Cache;
use Phpforce\Common\AbstractHasDispatcher;
use Phpforce\Metadata\MetadataFactory;
use Phpforce\SoapClient\Soap\SoapConnection;
use Phpforce\SoapClient\Result;
use Phpforce\SoapClient\Event;
use Phpforce\SoapClient\Exception;
use Phpforce\SoapClient\Soap\SoapConnectionFactory;

/**
 * A client for the Salesforce SOAP API
 *
 * @author David de Boer <david@ddeboer.nl>
 */
abstract class Client extends AbstractHasDispatcher implements ClientInterface
{
    /**
     * SOAP session header
     *
     * @var \SoapHeader
     */
    protected $sessionHeader;

    /**
     * Login result
     *
     * @var Result\LoginResult
     */
    protected $loginResult;

    /**
     * PHP SOAP client for interacting with the Salesforce API
     *
     * @var SoapConnection
     */
    private $connection;

    /**
     * Construct Salesforce SOAP client
     *
     * @param SoapConnection    $connection    SOAP client
     */
    public function __construct(SoapConnection $connection, $username, $password, $token)
    {
        $this->setConnection($connection);
    }

    /**
     * {@inheritdoc}
     */
    public function convertLead(array $leadConverts)
    {
        return $this->call(
            'convertLead',
            array(
                'leadConverts' => $leadConverts
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function emptyRecycleBin(array $ids)
    {
        $result = $this->call(
            'emptyRecycleBin',
            array('ids'   => $ids)
        );

        return $this->checkResult($result, $ids);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $objects, $type = null)
    {
        $result = $this->call(
            'create',
            array('sObjects' => $sobjects = $this->createObjectsSoapVars($objects, $type))
        );

        return $this->checkResult($result, $sobjects);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(array $ids)
    {
        $result = $this->call(
            'delete',
            array('ids' => $ids)
        );

        return $this->checkResult($result, $ids);
    }

    /**
     * {@inheritdoc}
     */
    public function describeGlobal()
    {
        if($this->getConnection()->getCache()->contains('__global_describe'))
        {
            return $this->getConnection()->getCache()->fetch('__global_describe');
        }
        $global = $this->call('describeGlobal');

        $this->getConnection()->getCache()->save('__global_describe', $global);

        return $global;
    }

    /**
     * {@inheritdoc}
     */
    public function describeSObjects(array $objects)
    {
        $retVal = array();

        $toFetch = array();

        foreach($objects AS $type)
        {
            if($this->getConnection()->getCache()->contains($type))
            {
                $retVal[] = $this->getConnection()->getCache()->fetch($type);
            }
            else
            {
                $toFetch[] = $type;
            }
        }

        if(count($toFetch) > 0)
        {
            foreach($this->call('describeSObjects', $toFetch) AS $metadatum)
            {
                $this->getConnection()->getCache()->save($metadatum->getName(), $metadatum);

                $retVal[] = $metadatum;
            }
        }
        return $retVal;
    }

    /**
     * {@inheritdoc}
     */
    public function describeTabs()
    {
        return $this->call('describeTabs');
    }

    /**
     * {@inheritdoc}
     */
    public function getDeleted($objectType, \DateTime $startDate, \DateTime $endDate)
    {
        return $this->call(
            'getDeleted',
            array(
                'sObjectType'   => $objectType,
                'startDate'     => $startDate,
                'endDate'       => $endDate
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdated($objectType, \DateTime $startDate, \DateTime $endDate)
    {
        return $this->call(
            'getUpdated',
            array(
                'sObjectType'   => $objectType,
                'startDate'     => $startDate,
                'endDate'       => $endDate
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo()
    {
        return $this->call('getUserInfo');
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateSessions(array $sessionIds)
    {
        throw new \BadMethodCallException('Not yet implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function login($username, $password, $token)
    {
        $this->loginResult = $this->connection->login
        (
            array(
                'username'  => $username,
                'password'  => $password.$token
            )
        )->result;

        return $this->loginResult;
    }

    /**
     * Get login result
     *
     * @return Result\LoginResult
     */
    public function getLoginResult()
    {
        return $this->loginResult;
    }

    /**
     * {@inheritdoc}
     */
    public function logout()
    {
        $this->call('logout');
        $this->loginResult = null;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $mergeRequests, $type)
    {
        foreach ($mergeRequests as $mergeRequest)
        {
            if ( ! ($mergeRequest instanceof Request\MergeRequest))
            {
                throw new \InvalidArgumentException(
                    'Each merge request must be an instance of MergeRequest'
                );
            }

            if ( ! $mergeRequest->masterRecord || !is_object($mergeRequest->masterRecord))
            {
                throw new \InvalidArgumentException('masterRecord must be an object');
            }

            if ( ! $mergeRequest->masterRecord->Id)
            {
                throw new \InvalidArgumentException('Id for masterRecord must be set');
            }

            if ( ! is_array($mergeRequest->recordToMergeIds))
            {
                throw new \InvalidArgumentException('recordToMergeIds must be an array');
            }

            $mergeRequest->masterRecord = new \SoapVar
            (
                $this->createSObject($mergeRequest->masterRecord, $type),
                SOAP_ENC_OBJECT,
                $type,
                $this->connection->getWsdl()->getTns()
            );
        }

        return $this->call(
            'merge',
            array('request' => $mergeRequests)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function process(array $processResults)
    {
        throw new \BadMethodCallException('Not yet implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function query($query)
    {
        return new Result\RecordIterator($this, $this->call(
            'query',
            array('queryString' => $query)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function queryAll($query)
    {
        return new Result\RecordIterator($this, $this->call(
            'queryAll',
            array('queryString' => $query)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function queryMore($queryLocator)
    {
        return new Result\RecordIterator($this, $this->call(
            'queryMore',
            array('queryLocator' => $queryLocator)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve(array $fields, array $ids, $objectType)
    {
        return $this->call(
            'retrieve',
            array(
                'fieldList'   => implode(',', $fields),
                'sObjectType' => $objectType,
                'ids'         => $ids
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function search($searchString)
    {
        return $this->call(
            'search',
            array(
                'searchString'  => $searchString
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function undelete(array $ids)
    {
        $result = $this->call(
            'undelete',
            array('ids' => $ids)
        );

        return $this->checkResult($result, $ids);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $objects, $type = null)
    {
        $result = $this->call(
            'update',
            array('sObjects' => $sobjects = $this->createObjectsSoapVars($objects, $type))
        );
        return $this->checkResult($result, $sobjects);
    }

    /**
     * {@inheritdoc}
     */
    public function upsert($externalIdFieldName, array $objects, $type)
    {
        return $this->call(
            'upsert',
            array(
                'externalIDFieldName' => $externalIdFieldName,
                'sObjects'            => $this->createObjectsSoapVars($objects, $type)
            )
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return Result\GetServerTimestampResult
     */
    public function getServerTimestamp()
    {
        return $this->call('getServerTimestamp');
    }

    /**
     * {@inheritdoc}
     */
    public function resetPassword($userId)
    {
        throw new \BadMethodCallException('Not yet implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function sendEmail(array $emails)
    {
        $result = $this->call(
            'sendEmail',
            array(
                'messages' => $this->createObjectsSoapVars($emails, 'SingleEmailMessage')
            )
        );

        return $this->checkResult($result, $emails);
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($userId, $password)
    {
        return $this->call(
            'setPassword',
            array(
                'userId'    => $userId,
                'password'  => $password
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function setConnection(SoapConnection $connection)
    {
        $this->connection = $connection;
    }


    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array
        (
            'loginResult' => $this->loginResult
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $unserialized = unserialize($serialized);

        $this->loginResult      = $unserialized['loginResult'];
    }

    /**
     * Turn Sobjects into \SoapVars
     *
     * @param array  $objects Array of objects
     * @param string $type    Object type
     *
     * @return \SoapVar[]
     */
    protected function createObjectsSoapVars(array $objects, $type = null)
    {
        $soapVars = array();

        foreach ($objects as $object)
        {
            $soapVars[] = $this->createObjectSoapVars($object, $type);
        }
        return $soapVars;
    }

    /**
     * @param $object
     * @param null|string $type
     */
    protected function createObjectSoapVars($object, $type = null)
    {
        if(is_array($object))
        {
            $object = (object)$object;
        }

        if($type === null && isset($object->type))
        {
            $type = $object->type;
        }
        if(null === $type)
        {
            throw new \InvalidArgumentException('Missing $type argument.');
        }
        return new \SoapVar($this->createSObject($object, $type), SOAP_ENC_OBJECT, $type, $this->connection->getWsdl()->getTns());
    }

    /**
     * Create a Salesforce object
     *
     * Converts PHP \DateTimes to their SOAP equivalents.
     *
     * @param object $object     Any object with public properties
     * @param string $objectType Salesforce object type
     *
     * @return \stdClass
     */
    protected function createSObject($object, $objectType)
    {
        $sObject = new \stdClass();

        $fieldsToNull = array();

        foreach (get_object_vars($object) AS $field => $value)
        {
            if(null === $value)
            {
                $fieldsToNull[] = $field;
            }
            elseif(is_scalar($value))
            {
                $sObject->$field = $this->convertFieldForDML($objectType, $field, $value);
            }
        }

        if(count($fieldsToNull) > 0)
        {
            $sObject->fieldsToNull = new \SoapVar(
                new \SoapVar(
                    sprintf('<fieldsToNull>%s</fieldsToNull>', implode('</fieldsToNull><fieldsToNull>', $fieldsToNull)),
                    XSD_ANYXML
                ),
                SOAP_ENC_ARRAY
            );
        }

        return $sObject;
    }

    /**
     * @param string $field
     * @param string $sfType
     * @return string
     */
    protected function convertFieldForDML($objectType, $field, $value)
    {
        // As PHP \DateTime to SOAP dateTime conversion is not done
        // automatically with the SOAP typemap for sObjects, we do it here.
        $results = $this->describeSobjects(array($objectType));

        switch($results[0]->getField($field)->getType())
        {
            case 'date':
                if ($value instanceof \DateTime)
                {
                    $value  = $value->format('Y-m-d');
                }
                break;
            case 'dateTime':
                if ($value instanceof \DateTime)
                {
                    $value  = $value->format('Y-m-d\TH:i:sP');
                }
                break;
            case 'base64Binary':
                $value = base64_encode($value);
                break;
        }
        return $value;
    }

    /**
     * Check response for errors
     *
     * Add each submitted object to its corresponding success or error message
     *
     * @param array $results Results
     * @param array $params  Parameters
     *
     * @throws Exception\SaveException  When Salesforce returned an error
     * @return array
     */
    protected function checkResult(array $results, array $params)
    {
        $exceptions = new Exception\SaveException();

        for ($i = 0; $i < count($results); $i++) {

            // If the param was an (s)object, set it’s Id field
            if (is_object($params[$i])
                && (!isset($params[$i]->Id) || null === $params[$i]->Id)
                && $results[$i] instanceof Result\SaveResult)
            {
                $params[$i]->Id = $results[$i]->getId();
            }

            if (!$results[$i]->isSuccess()) {
                $results[$i]->setParam($params[$i]);
                $exceptions->add($results[$i]);
            }
        }

        if ($exceptions->count() > 0) {
            throw $exceptions;
        }

        return $results;
    }

    /**
     * Issue a call to Salesforce API
     *
     * @param string $method SOAP operation name
     * @param array  $params SOAP parameters
     *
     * @return array | \Traversable An empty array or a result object, such
     *                              as QueryResult, SaveResult, DeleteResult.
     */
    protected function call($method, array $params = array())
    {
        // Prepare headers
        $this->connection->__setSoapHeaders($this->getSessionHeader());

        $requestEvent = new Event\RequestEvent($method, $params);

        $this->dispatch(Events::REQUEST, $requestEvent);

        try
        {
            $result = $this->connection->$method($params);
        }
        catch (\SoapFault $soapFault)
        {
            $faultEvent = new Event\FaultEvent($soapFault, $requestEvent);

            $this->dispatch(Events::FAULT, $faultEvent);

            throw $soapFault;
        }
        
        // No result e.g. for logout, delete with empty array
        if ( ! isset($result->result))
        {
            return array();
        }

        $this->dispatch
        (
            Events::RESPONSE,
            new Event\ResponseEvent($requestEvent, $result->result)
        );

        return $result->result;
    }

    /**
     * Set soap headers
     *
     * @param array $headers
     */
    protected function setSoapHeaders(array $headers)
    {
        $soapHeaderObjects = array();

        foreach ($headers as $key => $value)
        {
            $soapHeaderObjects[] = new \SoapHeader($this->connection->getWsdl()->getTns(), $key, $value);
        }
        $this->connection->__setSoapHeaders($soapHeaderObjects);
    }

    /**
     * Creates the session header if a valid login result
     * is present.
     *
     * @return \SoapHeader|null
     */
    protected function getSessionHeader()
    {
        if(null === $this->loginResult)
        {
            $this->sessionHeader = null;
        }
        elseif(null === $this->sessionHeader)
        {
            // ASSUME SESSION HEADER IS NEW; SO LOGIN RESULT IS FRESH ALSO
            $this->setEndpointLocation($this->loginResult->getServerUrl());

            $this->sessionHeader = new \SoapHeader
            (
                $this->connection->getWsdl()->getTns(),
                'SessionHeader',
                array(
                    'sessionId' => $this->loginResult->getSessionId()
                )
            );
        }
        return $this->sessionHeader;
    }

    /**
     * After successful log in, Salesforce wants us to change the endpoint
     * location
     *
     * @param string $location
     */
    protected function setEndpointLocation($location)
    {
        $this->connection->__setLocation($location);
    }
}