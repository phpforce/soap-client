<?php
namespace Phpforce\SoapClient\Exception;

class DeleteException extends \RuntimeException
{
    protected $successes;

    protected $errors;

    public function getSuccesses()
    {
        return $this->successes;
    }

    public function setSuccesses($successes)
    {
        $this->successes = $successes;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setErrors($errors)
    {
        $this->errors = $errors;
    }
}