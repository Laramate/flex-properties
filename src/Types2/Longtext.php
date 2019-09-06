<?php

namespace Laramate\FlexProperties\Types2;

use Laramate\FlexProperties\Exceptions\InvalidFlexPropertyValueException;
use Laramate\FlexProperties\Interfaces\FlexProperty;
use Laramate\FlexProperties\Property;

class Longtext extends Property
{
    /**
     * Database table.
     *
     * @var string
     */
    protected $table = 'flex_property_longtexts';

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

        $this->value = $value;

        return $this;
    }
}
