<?php

namespace Ddeboer\Salesforce\ClientBundle\Soap\Converter;

use BeSimple\SoapCommon\Converter\DateTimeTypeConverter as BaseDateTimeTypeConverter;

/**
 * This dateTime takes care of converting all dateTimes returned by Salesforce,
 * which are in UTC, are converted to the local timezone
 *
 * @author David de Boer <david@ddeboer.nl>
 *
 */
class DateTimeTypeConverter extends BaseDateTimeTypeConverter
{
    public function convertXmlToPhp($data)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($data);

        if ('' === $doc->textContent) {
            return null;
        }

        $dateTime = new \DateTime($doc->textContent);
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        return $dateTime;
    }
}

