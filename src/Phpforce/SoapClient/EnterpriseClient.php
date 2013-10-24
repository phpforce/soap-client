<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 23.10.13
 * Time: 16:16
 */

namespace Phpforce\SoapClient;

class EnterpriseClient extends Client
{
    /**
     * @param object $object
     *
     * @return object $object
     */
    public function sfToPhp($object)
    {
        if($object instanceof Result\QueryResult)
        {
            return new Result\RecordIterator($this, $object);
        }
        elseif(is_object($object))
        {
            foreach($object AS &$value)
            {
                $value = $this->sfToPhp($value);
            }
        }
        return $object;
    }
} 