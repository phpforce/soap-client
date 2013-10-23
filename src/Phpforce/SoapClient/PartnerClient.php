<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 23.10.13
 * Time: 16:16
 */

namespace Phpforce\SoapClient;

class PartnerClient extends Client
{
    /**
     * {@inheritdoc}
     */
    public function query($query)
    {
        $result = $this->call(
            'query',
            array('queryString' => $query)
        );

        return new Result\LooseRecordIterator($this, $result);
    }

    /**
     * {@inheritdoc}
     */
    public function queryAll($query)
    {
        $result = $this->call(
            'queryAll',
            array('queryString' => $query)
        );

        return new Result\LooseRecordIterator($this, $result);
    }

    /**
     * @param Result\SObject $current
     * @param string $xml
     */
    public function cleanupAnyXml(Result\SObject $current, $any)
    {
        $any = (array)$any;

        foreach($any AS $name => $value)
        {
            // atomic fields, parse XML!
            if(is_string($value))
            {
                $xml = <<<EOT
<any
    targetNamespace="urn:partner.soap.sforce.com"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:sf='urn:partner.soap.sforce.com'>$value</any>

EOT;
                $element = new \SimpleXMLElement($xml);

                foreach($element->children('sf', true) AS $key => $value)
                {
                    /** @var $value \SimpleXMLElement */
                    $attrs = $value->attributes('xsi', true);

                    if(isset($attrs['nil']) && (string)$attrs['nil'] === 'true')
                    {
                        $val = null;
                    }
                    else
                    {
                        $val = (string)$value;
                    }
                    $current->$key = $val;
                }
            }

            // n:1 relationship
            elseif($value instanceof Result\SObject && isset($value->any))
            {
                $this->cleanupAnyXml($value, $value->any);

                $current->$name = $value;
            }

            // 1:n relationship
            elseif($value instanceof Result\QueryResult)
            {
                $current->$name = new Result\LooseRecordIterator($this, $value);
            }
        }
        unset($current->any);
    }
} 