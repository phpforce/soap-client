<?php
/**
 * Created by PhpStorm.
 * User: joshi
 * Date: 23.10.13
 * Time: 16:16
 */

namespace Phpforce\SoapClient;

use Phpforce\SoapClient\Result\RecordIterator;

class PartnerClient extends Client
{
    /**
     * @var callable
     */
    private $sfToPhpConverter;

    /**
     * {@inheritdoc}
     */
    public function query($query)
    {
        $res = parent::query($query);

        $res->setSfToPhpConverter($this->getSfToPhpConverter());

        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function queryAll($query)
    {
        $res = parent::queryAll($query); // TODO: Change the autogenerated stub

        $res->setSfToPhpConverter($this->getSfToPhpConverter());

        return $res;
    }

    /**
     * @return callable
     */
    public function getSfToPhpConverter()
    {
        if(null === $this->sfToPhpConverter)
        {
            $self = $this;

            $this->sfToPhpConverter = function($object) use($self)
            {
                if($object instanceof Result\QueryResult)
                {
                    return new RecordIterator($self, $object);
                }
                elseif(is_object($object))
                {
                    $object->Id = $object->Id[0];

                    if(isset($object->any))
                    {
                        $self->cleanupAnyXml($object);
                    }
                }
                return $object;
            };
        }
        return $this->sfToPhpConverter;
    }

    /**
     * @param object $object
     */
    private function cleanupAnyXml($object)
    {
        $any = (array)$object->any;

        $objectDescribes = $this->describeSObjects(array($object->type));

        $objectDescribe = $objectDescribes[0];

        foreach($any AS $name => $value)
        {
            // atomic fields, parse XML!
            if(is_string($value))
            {
                $xml = <<<EOT
<any
    targetNamespace="{$this->getConnection()->getWsdl()->getTns()}"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:sf="{$this->getConnection()->getWsdl()->getTns()}">$value</any>

EOT;
                $element = new \SimpleXMLElement($xml);

                foreach($element->children('sf', true) AS $key => $v)
                {
                    /** @var $value \SimpleXMLElement */
                    $attrs = $v->attributes('xsi', true);

                    if(isset($attrs['nil']) && (string)$attrs['nil'] === 'true')
                    {
                        $val = null;
                    }
                    else
                    {
                        switch($objectDescribe->getField($key)->getType())
                        {
                            case 'date':
                            case 'datetime':
                                $val = new \DateTime((string)$v);
                            break;
                            case 'base64Binary':
                                $val = base64_decode((string)$v);
                                break;
                            default:
                                $val = (string)$v;
                                break;
                        }
                    }
                    $object->$key = $val;
                }
            }
            else
            {
                $object->$name = call_user_func($this->getSfToPhpConverter(), $value);
            }
        }
        unset($object->any);
    }
} 