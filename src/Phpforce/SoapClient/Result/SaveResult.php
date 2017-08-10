<?php

namespace Phpforce\SoapClient\Result;

/**
 * Save result
 *
 * @link http://www.salesforce.com/us/developer/docs/api/Content/sforce_api_calls_update_saveresult.htm
 *
 */
class SaveResult
{
    /**
     * Record id
     *
     * From the Salesforce docs:
     * "If this field is empty, then the object was not updated and the API
     * returned error information instead."
     *
     * @var string
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $success;

    /**
     * @var array
     */
    protected $errors;

    /**
     * @var mixed
     */
    protected $param;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return Error[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return mixed
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * @param mixed $param
     */
    public function setParam($param)
    {
        $this->param = $param;
    }
}