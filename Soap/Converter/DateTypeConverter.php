<?php

namespace Ddeboer\Salesforce\ClientBundle\Soap\Converter;

use BeSimple\SoapCommon\Converter\DateTypeConverter as BaseDateTypeConverter;

/**
 * This dateTime takes care of converting all dateTimes returned by Salesforce,
 * which are in UTC, are converted to the local timezone
 *
 * @author David de Boer <david@ddeboer.nl>
 *
 */
class DateTypeConverter extends BaseDateTypeConverter
{
    public function convertXmlToPhp($data)
    {
        $dateTime = parent::convertXmlToPhp($data);
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        return new $dateTime;
    }
}