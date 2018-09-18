<?php

namespace Legacy\Database\Doctrine;

use DateTime;

/**
 * Zero/Null/Broken-default-value datetime wrapper.
 *
 * Some SQL datetime fields accept as '0000-00-00 00:00:00'
 * valid value. This value can't be represented as normal
 * PHP DateTime object.
 *
 * We still want to use doctrine data types
 * with models which might have those broken
 * datetime's as value.
 *
 * This DateTime wrapper can be used to
 * insert those broken values to database
 * or test, if some row has that value.
 *
 * Assuming that doctrine always calls
 * format method, when trying to convert
 * PHP DateTime to SQL datetime.
 * If not, this won't work.
 */
class ZeroDateTime extends DateTime
{

    /**
     * @override
     */
    public function format($format)
    {
        return '0000-00-00 00:00:00';
    }

}
