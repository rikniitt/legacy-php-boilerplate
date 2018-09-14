<?php

namespace Legacy\Database\Doctrine;

use Doctrine\DBAL\Types\DateTimeType as DoctrineDateTimeType;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Carbon\Carbon;
use DateTime;

/**
 * Custom Doctrine date time type.
 *
 * Wraps value to Carbon instance.
 */
class DateTimeType extends DoctrineDateTimeType
{

    // @codingStandardsIgnoreLine
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value;
        }

        /**
         * Database, which we are working with, contains many datetime columns
         * with '0000-00-00 00:00:00' as default value.
         * PHP DateTime object created with it will be set to date
         * '-0001-11-30 00:00:01'.
         * This should be probably treated as "value missing"
         * rather than "proper value".
         */

        if ($value === '0000-00-00 00:00:00') {
            return null;
        }

        if ($value instanceof DateTime) {
            $formatted = $value->format('Y-m-d H:i:s');

            if ($formatted === '-0001-11-30 00:00:01') {
                // Value missing.
                return null;
            } else {
                // Wrap to carbon.
                return Carbon::createFromFormat('Y-m-d H:i:s', $formatted);
            }
        }

        $dt = Carbon::createFromFormat($platform->getDateTimeFormatString(), $value);

        if (!$dt) {
            // Fallback. Just try to parse
            $dt = new Carbon($value);
        }

        if (!$dt) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        return $dt;
    }

}
