<?php

namespace Laramate\FlexProperties\Types;

use Laramate\FlexProperties\Exceptions\InvalidFlexPropertyValueException;

class StringFlexProperty extends FlexProperty
{
    const MAX_LENGTH = 100;

    /**
     * Set calue.
     *
     * @param $value
     *
     * @throws InvalidFlexPropertyValueException
     */
    public function setValueAttribute($value)
    {
        if (!is_string($value)) {
            throw new InvalidFlexPropertyValueException('$value must be a string');
        }

        if (strlen($value) > self::MAX_LENGTH) {
            throw new InvalidFlexPropertyValueException('$value must be less than ' . self::MAX_LENGTH . ' charachters');
        }

        $this->attributes['value'] = $value;
    }
}
