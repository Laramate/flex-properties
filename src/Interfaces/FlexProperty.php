<?php

namespace Laramate\FlexProperties\Interfaces;

interface FlexProperty
{
    /**
     * Get the database table name.
     *
     * @return string
     */
    public function getTable(): string;
}
