<?php

namespace Laramate\FlexProperties;

use ArrayAccess;
use Illuminate\Support\Str;
use Laramate\FlexProperties\Interfaces\FlexProperty;

abstract class Property implements FlexProperty, ArrayAccess
{
    protected $table;
    protected $name;
    protected $value;
    protected $config;
    protected $locale;
    protected $attached;

    public function __construct(string $name, array $config = [])
    {
        $this->name = $name;
        $this->config = $config;
    }

    public function offsetExists($offset)
    {
        $method = Str::camel('get_'.$offset);

        return method_exists($this, $method);
    }

    public function offsetGet($offset)
    {
        $method = Str::camel('get_'.$offset);

        return $this->$method();
    }

    public function offsetSet($offset, $value)
    {
        $method = Str::camel('set_'.$offset);

        if (method_exists($this, $method)) {
            $this->$method($value);
        }

        return $this;
    }

    public function offsetUnset($offset)
    {
        return $this->offsetSet($offset, null);
    }

    public function attach(&$model): FlexProperty
    {
        $this->attached = $model;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getLocale(): string
    {
        return $this->name;
    }

    /**
     * Get the database table name.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    public function setLocale(string $locale): FlexProperty
    {
        $this->locale = $locale;

        return $this;
    }

    public function setValue($value = null): FlexProperty
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return is_scalar($this->value) ? (string) $this->value : '';
    }
}
