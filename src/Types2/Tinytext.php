<?php

namespace Laramate\FlexProperties\Types2;

use Laramate\FlexProperties\Exceptions\InvalidFlexPropertyValueException;
use Laramate\FlexProperties\Interfaces\FlexProperty;
use Laramate\FlexProperties\Property;

class Tinytext extends Property
{
    const MAX_LENGTH = 20;

    /**
     * Database table.
     *
     * @var string
     */
    protected $table = 'flex_property_tinytexts';

    /**
     * Set value.
     *
     * @param null $value
     *
     * @throws InvalidFlexPropertyValueException
     *
     * @return FlexProperty
     */
    public function setValue($value = null): FlexProperty
    {
        if (! is_string($value)) {
            throw new InvalidFlexPropertyValueException('$value must be a string');
        }

        $this->value = count_chars($value) > self::MAX_LENGTH
            ? substr($value, 0, self::MAX_LENGTH)
            : $value;

        return $this;
    }
}
