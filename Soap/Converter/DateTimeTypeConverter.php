<?php

namespace Ddeboer\Salesforce\ClientBundle\Soap\Converter;

use BeSimple\SoapCommon\Converter\DateTimeTypeConverter as BaseDateTimeTypeConverter;

/**
 * Converts all dateTimes as they are returned by Salesforce, in UTC, to the
 * local timezone
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class DateTimeTypeConverter extends BaseDateTimeTypeConverter
{
    public function convertXmlToPhp($data)
    {
        $dateTime = parent::convertXmlToPhp($data);
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        return $dateTime;
    }
}