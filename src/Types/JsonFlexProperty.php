<?php

namespace Laramate\FlexProperties\Types;

class JsonFlexProperty extends FlexProperty
{
    /**
     * Database table.
     *
     * @var string
     */
    protected $table = 'json_flex_properties';

    /**
     * Set value.
     *
     * @param string|array $value
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get value.
     *
     * @param string $value
     *
     * @return array
     */
    public function getValueAttribute($value)
    {
        return json_decode($value);
    }
}
