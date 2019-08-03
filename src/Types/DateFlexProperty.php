<?php

namespace Laramate\FlexProperties\Types;

use Laramate\FlexProperties\Exceptions\InvalidFlexPropertyValueException;

class DateFlexProperty extends FlexProperty
{
    /**
     * Set value.
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

        $this->attributes['value'] = $value;
    }
}
