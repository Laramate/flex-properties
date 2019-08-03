<?php

namespace LaravelFlexProperties;

use LaravelFlexProperties\Exceptions\FlexPropertyException;
use LaravelFlexProperties\Types\FlexProperty;

class Flex
{
    /**
     * Flex property type definitions.
     *
     * @var array
     */
    protected static $property_types = [];

    /**
     * Stores if the static boot has been executed.
     *
     * @var bool
     */
    protected static $static_booted = false;

    /**
     * Static boot.
     *
     * @throws FlexPropertyException
     */
    public static function bootStatic()
    {
        if (static::$static_booted) {
            return false;
        }

        static::registerFlexPropertyTypes();
        static::$static_booted = true;
    }

    /**
     * Load flex property types from config.
     *
     * @throws FlexPropertyException
     */
    protected static function registerFlexPropertyTypes()
    {
        $types = config('flex-properties.types');

        if (!is_array($types)) {
            throw new FlexPropertyException('Flex property type configuration is missing');
        }

        self::$property_types = $types;
    }

    /**
     * Get available flex property types.
     *
     * @see $property_types
     * @throws FlexPropertyException     *
     *
     * @return array
     */
    public static function types(): array
    {
        static::bootStatic();

        return static::$property_types;
    }


    /**
     * Get flex object class name by type.
     *
     * @param string $type
     * @throws FlexPropertyException
     *
     * @return string
     */
    public static function getClass(string $type): string
    {
        return static::$property_types[
            static::typeOrFail($type)
        ];
    }

    /**
     * Determinate if a flex property type exists.
     *
     * @param string $type
     * @throws FlexPropertyException
     *
     * @return bool
     */
    public static function typeExists(string $type): bool
    {
        self::bootStatic();
        return array_key_exists($type, self::$property_types);
    }

    /**
     * Determinate if a flex property type exists and throw an exception if not.
     *
     * @param string $type
     * @throws FlexPropertyException
     *
     * @return string
     */
    public static function typeOrFail(string $type): string
    {
        if (!self::typeExists($type)) {
            throw new FlexPropertyException("Flex property type '{$type}' is invalid");
        }

        return $type;
    }

    /**
     * Flex property object factory.
     *
     * @param string $type
     * @param array $attributes
     * @throws FlexPropertyException
     *
     * @return FlexProperty
     */
    public static function factory(string $type, array $attributes=[])
    {
        $class = static::getClass($type);

        return new $class($attributes);
    }

    /**
     * Builds a where statement to query flex properties.
     *
     * Use this helper to query your custom flex properties. Simply use this helper within
     * any where(), orWhere() and andWhere() function.
     *
     * For example:
     * ```php
     * $model->where(Flex::where('flex_property', ...$arguments));
     * ```
     *
     * @param string $name
     * @param mixed ...$arguments
     *
     * @return \Closure
     */
    public static function where(string $name, ...$arguments)
    {
        return function($query) use ($name, $arguments) {
            foreach (static::types() as $type=>$class) {
                $query->orWhere(function($subquery) use ($type, $name, $arguments) {
                    $subquery->where('flex_tbl_' . $type . '.value', ...$arguments);
                    $subquery->where('flex_tbl_' . $type . '.name', $name);
                    $subquery->where('flex_tbl_' . $type . '.linkable_type', get_class($subquery->getModel()));
                });
            }
        };
    }
}