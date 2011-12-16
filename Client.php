<?php

namespace Ddeboer\Salesforce\ClientBundle;

use Ddeboer\Salesforce\ClientBundle\Request;
use Ddeboer\Salesforce\ClientBundle\Response;
use Ddeboer\Salesforce\ClientBundle\Event;
use Ddeboer\Salesforce\ClientBundle\Soap\SoapClient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * A client for the Salesforce SOAP API
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class Client
{
    /**
     * SOAP namespace
     *
     * @var string
     */
    const SOAP_NAMESPACE = 'urn:enterprise.soap.sforce.com';

    /**
     * SOAP session header
     *
     * @var \SoapHeader
     */
    protected $sessionHeader;

    /**
     * PHP SOAP client for interacting with the Salesforce API
     *
     * @var SoapClient
     */
    protected $soapClient;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Type collection as derived from the WSDL
     *
     * @var array
     */
    protected $types = array();

    /**
     * Construct Salesforce SOAP client
     *
     * @param \SoapClient $soapClient
     * @param string $username  Salesforce username
     * @param string $password  Salesforce password
     * @param string $token     Salesforce security token
     */
    public function __construct(SoapClient $soapClient, $username, $password, $token)
    {
        $this->soapClient = $soapClient;
        $this->username = $username;
        $this->password = $password;
        $this->token = $token;
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * Create one or more Salesforce objects
     *
     * @param array $objects    Array of Salesforce objects
     * @param string $type      E.g. Account or Contact
     * @return Response\SaveResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_create.htm
     */
    public function create(array $objects, $type)
    {
        return $this->call('create', array(
            'sObjects'            => $this->createSoapVars($objects, $type)
        ));
    }

    /**
     * Deletes one or more records from your organization’s data
     *
     * @param array $ids    Salesforce object IDs
     * @return Response\DeleteResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_delete.htm
     */
    public function delete(array $ids)
    {
        return $this->call('delete', array(
            'ids'   => $ids
        ));
    }

    /**
     * Retrieves a list of available objects for your organization’s data

     * @return Response\DescribeGlobalResult
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_describeglobal.htm
     */
    public function describeGlobal()
    {
        return $this->call('describeGlobal');
    }

    /**
     * describes metadata (field list and object properties) for the specified object or array of objects
     * 
     * @param array $objects
     * @param boolean $cache    Whether it’s okay to fetch results from cache
     * @return Response\DescribeSObject[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_describesobjects.htm
     */
    public function describeSObjects(array $objects, $cache = true)
    {
        if (true === $cache) {

        }
        return $this->call('describeSObjects', $objects);
    }

    /**
     * Get user info
     *
     * @return Response\GetUserInfoResult
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_getuserinfo.htm
     */
    public function getUserInfo()
    {
        return $this->call('getUserInfo');
    }

    /**
     * Logs in to the login server and starts a client session
     *
     * @param string $username  Salesforce username
     * @param string $password  Salesforce password
     * @param string $token     Salesforce security token
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_login.htm
     */
    public function login($username, $password, $token)
    {
        $result = $this->soapClient->login(array(
            'username'  => $username,
            'password'  => $password.$token
        ));

        $this->setEndpointLocation($result->result->serverUrl);
        $this->setSessionId($result->result->sessionId);
    }

    /**
     * Merge a Salesforce lead, contact or account with one or two other
     * Salesforce leads, contacts or accounts
     *
     * @param array $mergeRequests  Array of merge request objects
     * @return Response\MergeResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_merge.htm
     */
    public function merge(array $mergeRequests, $type)
    {
        foreach ($mergeRequests as $mergeRequest) {
            if (!($mergeRequest instanceof Request\MergeRequest)) {
                throw new \InvalidArgumentException(
                    'Each merge request must be an instance of MergeRequest'
                );
            }

            if (!$mergeRequest->masterRecord || !is_object($mergeRequest->masterRecord)) {
                throw new \InvalidArgumentException('masterRecord must be an object');
            }

            if (!$mergeRequest->masterRecord->Id) {
                throw new \InvalidArgumentException('Id for masterRecord must be set');
            }

            if (!is_array($mergeRequest->recordToMergeIds)) {
                throw new \InvalidArgumentException('recordToMergeIds must be an array');
            }

            $mergeRequest->masterRecord = new \SoapVar(
                $this->createSObject($mergeRequest->masterRecord, $type),
                SOAP_ENC_OBJECT,
                $type,
                self::SOAP_NAMESPACE
            );
        }

        return $this->call('merge', array(
            'request'   => $mergeRequests
        ));
    }

    /**
     * Query salesforce API and return results as record iterator
     *
     * @param string $query
     * @return RecordIterator
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_query.htm
     */
    public function query($query)
    {
        $result = $this->call('query', array(
            'queryString'   => $query
        ));

        return new Response\RecordIterator($this, $result);
    }

    /**
     * Retrieves data from specified objects, whether or not they have been
     * deleted
     *
     * @param string $query
     * @return Response\QueryResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_queryall.htm
     */
    public function queryAll($query)
    {
        $result = $this->call('queryAll', array(
            'queryString'   => $query
        ));

        return new Response\RecordIterator($this, $result);
    }

    /**
     * Retrieves the next batch of objects from a query
     *
     * @param string $queryLocator
     * @return Response\QueryResult
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_querymore.htm
     */
    public function queryMore($queryLocator)
    {
        return $this->call('queryMore', array(
            'queryLocator'  => $queryLocator
        ));
    }

    public function retrieve(array $fields, $type, array $ids)
    {
        return $this->call('retrieve', array(
            'fieldList'     => implode(',', $fields),
            'sObjectType'   => $type,
            'ids'           => $ids
        ));
    }

    /**
     * Executes a text search in your organization’s data
     *
     * @param string $searchString
     * @return Response\SearchResult
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_search.htm
     */
    public function search($searchString)
    {
        return $this->call('search', array(
            'searchString'  => $searchString
        ));
    }

    /**
     * Updates one or more existing records in your organization’s data
     *
     * @param array $objects
     * @param string $type  Object type
     * @return Response\SaveResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_update.htm
     */
    public function update(array $objects, $type)
    {
        return $this->call('update', array(
            'sObjects'            => $this->createSoapVars($objects, $type)
        ));
    }

    /**
     * Creates new records and updates existing records; uses a custom field to
     * determine the presence of existing records
     *
     * @param string $externalIdFieldName
     * @param array $objects Array of objects
     * @param string $type   Object type
     * @return Response\SaveResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_upsert.htm
     */
    public function upsert($externalIdFieldName, array $objects, $type)
    {
        return $this->call('upsert', array(
            'externalIDFieldName' => $externalIdFieldName,
            'sObjects'            => $this->createSoapVars($objects, $type)
        ));
    }

    /**
     * Turn sObjects into a \SoapVar
     *
     * @param mixed $object
     * @return \SoapVar[]
     */
    protected function createSoapVars(array $objects, $type)
    {
        $soapVars = array();

        foreach ($objects as $object) {
            
            $object = $this->createSObject($object, $type);
            $soapVar = new \SoapVar($object, SOAP_ENC_OBJECT, $type, self::SOAP_NAMESPACE);

            if (isset($object->fieldsToNull) && $object->fieldsToNull) {
                $soapVar->enc_value->fieldsToNull = $this->fixFieldsToNullXml($soapVar);
            } else {
                unset($soapVar->enc_value->fieldsToNull);
            }

            $soapVars[] = $soapVar;
        }

        return $soapVars;
    }

    /**
     * Fix the fieldsToNull property for sObjects
     *
     * @param \SoapVar $object
     * @return \SoapVar
     */
    protected function fixFieldsToNullXml(\SoapVar $object)
    {
        if (isset($object->enc_value->fieldsToNull)
            && is_array($object->enc_value->fieldsToNull)
            && count($object->enc_value->fieldsToNull) > 0)
        {
            $xml = '';
            foreach ($object->enc_value->fieldsToNull as $fieldToNull) {
                $xml .= '<fieldsToNull>' . $fieldToNull . '</fieldsToNull>';
            }
            return new \SoapVar(new \SoapVar($xml, XSD_ANYXML), SOAP_ENC_ARRAY);
        }
    }

    /**
     * Check response for errors
     *
     * @param \stdClass $results
     * @throws \Exception       When Salesforce returned an error
     */
    protected function checkResult($results)
    {
        foreach ($results->result as $result) {
            if (isset($result->errors)) {
                if (null !== $this->eventDispatcher) {
                    foreach ($result->errors as $error) {
                        $event = new Event\ErrorEvent($error);
                        $this->eventDispatcher->dispatch(Events::clientError, $event);
                    }
                }

                // Use first error for throwing exception
                $error = /* @var $error Response\Error */  $result->errors[0];
                $errorMessage = 'Salesforce returned error ' . $error->statusCode .
                                ' for ID ' . $result->id
                . ': ' . $error->message . "\n"
                . json_encode($result->errors);

                throw new \InvalidArgumentException($errorMessage);
            }
        }
    }

    /**
     * Issue a call to Salesforce API
     *
     * @param string $method
     * @param array $params
     * @return mixed
     */
    protected function call($method, array $params = array())
    {
        $this->init();
        return $this->doCall($method, $params);
    }

    protected function init()
    {
        // If there’s no session header yet, this means we haven’t yet logged in
        if (!$this->getSessionHeader()) {
            $this->login($this->username, $this->password, $this->token);
        }
    }

    protected function doCall($method, array $params = array())
    {
          // Prepare headers
        $this->soapClient->__setSoapHeaders($this->getSessionHeader());

        if (null !== $this->eventDispatcher) {
            $requestEvent = new Event\RequestEvent($method, $params);
            $this->eventDispatcher->dispatch(Events::clientRequest, $requestEvent);
        }

        try {
            $result = $this->soapClient->$method($params);
        } catch (\SoapFault $soapFault) {
            if (null !== $this->eventDispatcher) {
                $event = new Event\SoapFaultEvent($soapFault);
                $this->eventDispatcher->dispatch(Events::clientSoapFault, $event);
            }

            throw $soapFault;
        }

        if (null !== $this->eventDispatcher) {
            $event = new Event\ResponseEvent($requestEvent, $result->result);
            $this->eventDispatcher->dispatch(Events::clientResponse, $event);
        }

        $this->checkResult($result);

        if (isset($result->result)) {
            return $result->result;
        }
    }

    /**
     * Set soap headers
     *
     * @param array $headers
     */
    protected function setSoapHeaders(array $headers)
    {
        $soapHeaderObjects = array();
        foreach ($headers as $key => $value) {
            $soapHeaderObjects[] = new \SoapHeader(self::SOAP_NAMESPACE, $key, $value);
        }

        $this->soapClient->__setSoapHeaders($soapHeaderObjects);
    }

    /**
     * Get session header
     *
     * @return \SoapHeader
     */
    protected function getSessionHeader()
    {
        return $this->sessionHeader;
    }

    /**
     * Save session id to SOAP headers to be used on subsequent requests
     *
     * @param string $sessionId
     */
    protected function setSessionId($sessionId)
    {
        $this->sessionHeader = new \SoapHeader(
            self::SOAP_NAMESPACE,
            'SessionHeader',
            array(
                'sessionId' => $sessionId
            )
        );
    }

    /**
     * After successful log in, Salesforce wants us to change the endpoint
     * location
     *
     * @param string $location
     */
    protected function setEndpointLocation($location)
    {
        $this->soapClient->__setLocation($location);
    }

    /**
     * Create a Salesforce object
     *
     * Converts PHP \DateTimes to their SOAP equivalents.
     *
     * @param mixed $object
     * @param string $objectType
     * @return \stdClass    SObject
     */
    protected function createSObject($object, $objectType)
    {
        $sObject = new \stdClass();

        foreach (get_object_vars($object) as $field => $value) {
            $type = $this->soapClient->getSoapElementType($objectType, $field);
            if (!$type) {
                continue;
            }

            // As PHP \DateTime to SOAP dateTime conversion is not done
            // automatically with the SOAP typemap for sObjects, we do it here.
            // TODO: also take in account the timezone difference!
            switch ($type) {
                case 'date':
                    if ($value instanceof \DateTime) {
                        $value  = $value->format('Y-m-d');
                    }
                    break;

                case 'dateTime':
                    if ($value instanceof \DateTime) {
                        $value  = $value->format('Y-m-d\TH:i:sP');
                    }
                    break;
            }

            $sObject->$field = $value;
        }
        return $sObject;
    }
}