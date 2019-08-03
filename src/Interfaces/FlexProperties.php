<?php

namespace Laramate\FlexProperties\Interfaces;

use Laramate\FlexProperties\Exceptions\FlexPropertyException;

interface FlexProperties
{
    /**
     * Get FlexProperty value.
     *
     * @param string $name
     *
     * @throws FlexPropertyException
     *
     * @return mixed
     */
    public function getFlexPropertyValue(string $name);

    /**
     * Set FlexProperty value.
     *
     * @param string $name
     * @param $value
     *
     * @throws FlexPropertyException
     *
     * @return $this
     */
    public function setFlexPropertyValue(string $name, $value);

    /**
     * Store all FlexProperty values.
     *
     * @see bootHasFlexProperties
     */
    public function storeFlexProperties();
}
