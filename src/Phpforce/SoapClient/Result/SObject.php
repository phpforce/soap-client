<?php

namespace Phpforce\SoapClient\Result;

/**
 * Standard object
 *
 */
class SObject
{
    /**
     * @var string
     */
    public $Id;
    
    public function getId()
    {
        return $this->Id;
    }

    public function __get($prop) {
        if (property_exists($this, $prop)) {
            return $this->$prop;
        } else {
            return "N/A";
        }
    }

    public function __call($method, $args) {
        if (isset($this->$method) && is_callable($method)) {
            $closure = $this->$method;
            call_user_func_array($closure, $args);
        }
    }
}
