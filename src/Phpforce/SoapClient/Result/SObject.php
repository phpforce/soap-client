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
     * @var string
     */
    private $type;

    /**
     * @param string $Id
     * @param string $sObjectType
     */
    public function __construct($Id, $sObjectType)
    {
        $this->Id = $Id;

        $this->type = $sObjectType;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @return string
     */
    public function getSobjectType()
    {
        return $this->type;
    }
}
