<?php
namespace Phpforce\SoapClient\Soap\TypeConverter;

/**
 * Converts between PHP \DateTime and SOAP date objects
 */
class DateTypeConverter implements TypeConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTypeNamespace()
    {
        return 'http://www.w3.org/2001/XMLSchema';
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeName()
    {
        return 'date';
    }

    /**
     * {@inheritdoc}
     */
    public function convertXmlToPhp($data)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($data);

        if ('' === $doc->textContent) {
            return null;
        }

        $dateTime = new \DateTime($doc->textContent);

        return $dateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function convertPhpToXml($php)
    {
        return sprintf('<%1$s>%2$s</%1$s>', $this->getTypeName(), $php->format('Y-m-d'));
    }
}

