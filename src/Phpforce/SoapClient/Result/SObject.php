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

    /**
     * @return string
     */
    public function getId()
    {
        return $this->Id;
    }
}
