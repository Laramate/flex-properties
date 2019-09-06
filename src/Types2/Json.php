<?php

namespace Laramate\FlexProperties\Types2;

use Laramate\FlexProperties\Property;

class Json extends Property
{
    /**
     * Database table.
     *
     * @var string
     */
    protected $table = 'flex_property_jsons';

    /**
     * Set value.
     *
     * @param string|array $value
     */
    public function setValueAttribute($value)
    {
        $this->value = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get value.
     *
     * @return array
     */
    public function getValue()
    {
        return json_decode($this->value);
    }
}
