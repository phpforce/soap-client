<?php

namespace Ddeboer\Salesforce\ClientBundle;

use Ddeboer\Salesforce\ClientBundle\Request;
use Ddeboer\Salesforce\ClientBundle\Response;

/**
 * Salesforce API client interface
 *
 * @author David de Boer <david@ddeboer.nl>
 */
interface ClientInterface
{
    /**
     * Converts a Lead into an Account, Contact, or (optionally) an Opportunity
     *
     * @param array $leadConverts   Array of LeadConvert
     */
    function convertLead(array $leadConverts);

    /**
     * Create one or more Salesforce objects
     *
     * @param array $objects      Array of Salesforce objects
     * @param string $objectType  Object type, e.g., account or contact
     * @return Response\SaveResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_create.htm
     */
    function create(array $objects, $objectType);

    /**
     * Deletes one or more records from your organization’s data
     *
     * @param array $ids    Salesforce object IDs
     * @return Response\DeleteResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_delete.htm
     */
    function delete(array $ids);

    /**
     * Retrieves a list of available objects for your organization’s data
     *
     * @return Response\DescribeGlobalResult
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_describeglobal.htm
     */
    function describeGlobal();

    /**
     * Describes metadata (field list and object properties) for the specified object or array of objects
     *
     * @param array $objectNames
     * @return Response\DescribeSObjectResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_describesobjects.htm
     */
    function describeSObjects(array $objectNames);

    /**
     * Returns information about the standard and custom apps available to the
     * logged-in
     *
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_invalidatesessions.htm
     */
    function describeTabs();

    /**
     * Delete records from the recycle bin immediately
     *
     * @param array $ids        Object IDS
     */
    function emptyRecycleBin(array $ids);

    /**
     * Retrieves the list of individual records that have been deleted within
     * the given timespan for the specified object
     *
     * @param string $objectType
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_getdeleted.htm
     */
    function getDeleted($objectType, \DateTime $startDate, \DateTime $endDate);

    /**
     * Retrieves the list of individual objects that have been updated (added or
     * changed) within the given timespan for the specified object
     *
     * @param string $objectType
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_getupdated.htm
     */
    function getUpdated($objectType, \DateTime $startDate, \DateTime $endDate);

    /**
     * Ends one or more sessions specified by a sessionId
     *
     * @param array $sessionIds
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_invalidatesessions.htm
     */
    function invalidateSessions(array $sessionIds);

    /**
     * Logs in to the login server and starts a client session
     *
     * @param string $username  Salesforce username
     * @param string $password  Salesforce password
     * @param string $token     Salesforce security token
     * @return Response\LoginResult
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_login.htm
     */
    function login($username, $password, $token);

    /**
     * Ends the session of the logged-in user
     *
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_logout.htm
     */
    function logout();

    /**
     * Merge a Salesforce lead, contact or account with one or two other
     * Salesforce leads, contacts or accounts
     *
     * @param array $mergeRequests  Array of merge request objects
     * @param string $objectType    Object type, e.g., account or contact
     * @return Response\MergeResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_merge.htm
     */
    function merge(array $mergeRequests, $objectType);

    /**
     * Submits an array of approval process instances for approval, or processes
     * an array of approval process instances to be approved, rejected, or
     * removed
     *
     * @param array $processRequests
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_process.htm
     */
    function process(array $processRequests);

    /**
     * Query salesforce API and return results as record iterator
     *
     * @param string $query
     * @return RecordIterator
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_query.htm
     */
    function query($query);

    /**
     * Retrieves data from specified objects, whether or not they have been
     * deleted
     *
     * @param string $query
     * @return Response\QueryResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_queryall.htm
     */
    function queryAll($query);

    /**
     * Retrieves the next batch of objects from a query
     *
     * @param string $queryLocator
     * @return Response\QueryResult
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_querymore.htm
     */
    function queryMore($queryLocator);

    /**
     * Retrieves one or more records based on the specified IDs
     *
     * @param array $fields     Fields to retrieve on the object
     * @param array $ids        IDs of objects to retrieve
     * @param string $objectType    Object type, e.g., account or contact
     * @param array $ids
     *
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_retrieve.htm
     */
    function retrieve(array $fields, array $ids, $objectType);
    
    /**
     * Executes a text search in your organization’s data
     *
     * @param string $searchString
     * @return Response\SearchResult
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_search.htm
     */
    function search($searchString);

    /**
     * Undeletes records from the Recycle Bin
     *
     * @param array $ids
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_undelete.htm
     */
    function undelete(array $ids);

    /**
     * Updates one or more existing records in your organization’s data
     *
     * @param array $objects
     * @param string $objectType    Object type, e.g., account or contact
     * @return Response\SaveResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_update.htm
     */
    function update(array $objects, $objectType);

    /**
     * Creates new records and updates existing records; uses a custom field to
     * determine the presence of existing records
     *
     * @param string $externalIdFieldName
     * @param array $objects Array of objects
     * @param string $objectType    Object type, e.g., account or contact
     * @return Response\SaveResult[]
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_upsert.htm
     */
    function upsert($externalFieldName, array $objects, $objectType);

    /**
     * Retrieves the current system timestamp (Coordinated Universal Time (UTC)
     * time zone) from the API
     *
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_getservertimestamp.htm
     */
    function getServerTimestamp();

    /**
     * Get user info
     *
     * @return Response\GetUserInfoResult
     * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_getuserinfo.htm
     */
    function getUserInfo();

    /**
     * Changes a user’s password to a temporary, system-generated value
     * 
     * @param string $userId
     */
    function resetPassword($userId);

    /**
     * Immediately sends an email message
     *
     * @param array $emails
     */
    function sendEmail(array $emails);

    /**
     * Sets the specified user’s password to the specified value
     *
     * @param string $userId
     * @param string $password
     */
    function setPassword($userId, $password);    
}