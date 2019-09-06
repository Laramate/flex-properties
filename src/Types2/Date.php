<?php

namespace Laramate\FlexProperties\Types2;

use Laramate\FlexProperties\Interfaces\FlexProperty;
use Laramate\FlexProperties\Property;

class Date extends Property
{
    /**
     * Database table.
     *
     * @var string
     */
    protected $table = 'flex_property_dates';

    /**
     * Set value.
     *
     * @param null $value
     *
     * @return FlexProperty
     */
    public function setValue($value = null): FlexProperty
    {
        $this->value = $value;
    }
}
