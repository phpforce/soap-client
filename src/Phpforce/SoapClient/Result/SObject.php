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
}
