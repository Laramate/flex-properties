<?php

namespace LaravelFlexProperties\Traits;

use LaravelFlexProperties\Exceptions\FlexPropertyException;
use LaravelFlexProperties\Flex;
use Mindtwo\DynamicMutators\Facades\Handler;

trait HasFlexProperties
{
    /**
     * Locale settings.
     *
     * @see bootHasFlexProperties()
     *
     * @var array
     */
    protected static $locale = [
        'current'  => 'en',
        'default'  => 'en',
        'fallback' => 'en',
    ];

    /**
     * Flex property objects.
     *
     * @var array
     */
    protected $flex_objects = [];
    protected $flex_joins = [];
    protected $next_operation = [];

    /**
     * Boot trait.
     */
    public static function bootHasFlexProperties()
    {
        // Register mutators
        static::registerMutationHandler(Handler::make([
            'name'        => 'flex_properties',
            'get_mutator' => ['getFlexPropertyValue'],
            'set_mutator' => ['setFlexPropertyValue'],
        ]));

        // Locale settings
        static::$locale = [
            'current'  => config('app.locale'),
            'default'  => config('app.locale'),
            'fallback' => config('app.fallback_locale'),
        ];

        // Register event to store flex properties
        static::saved(function ($model) {
            $model->storeFlexProperties();
        });

        static::addGlobalScope('flex-property-join', function ($builder) {
            $builder->flexProperties();
        });
    }

    public function scopeFlexProperties($query)
    {
        collect($this->flex_properties)->flip()->each(function ($item, $type) use (&$query) {
            $property = Flex::factory($type);
            $tableAlias = 'flex_tbl_'.$type;
            $query->leftJoin($property->getTable().' AS '.$tableAlias, function ($join) use ($type, $tableAlias) {
                $join->on($tableAlias.'.linkable_id', $this->getTable().'.id');
                $join->where($tableAlias.'.linkable_type', static::class);
            });
        });

        $query->select($this->getTable().'.*');
    }

    /**
     * Determine if a FlexProperty is defined.
     *
     * @param string $name
     *
     * @return bool
     */
    protected function hasFlexProperty(string $name): bool
    {
        return array_key_exists($name, $this->flex_properties);
    }

    /**
     * Determine if a FlexProperty is defined and throw an exception if not.
     *
     * @param string $name
     *
     * @throws FlexPropertyException
     *
     * @return string
     */
    protected function hasFlexPropertyOrFail(string $name): string
    {
        if (! $this->hasFlexProperty($name)) {
            throw new FlexPropertyException(sprintf('FlexProperty "%s" is not defined', $name));
        }

        return $name;
    }

    /**
     * Get FlexProperty type.
     *
     * @param string $name
     *
     * @throws FlexPropertyException
     *
     * @return string
     */
    protected function getFlexPropertyType(string $name): string
    {
        $this->hasFlexPropertyOrFail($name);

        return Flex::typeOrFail($this->flex_properties[$name]);
    }

    /**
     * Return a reference to a flex property object.
     *
     * @param string      $name
     * @param string|null $locale
     *
     * @return mixed
     */
    protected function &flexPropertyReference(string $name, string $locale = null)
    {
        return $this->flex_objects[$locale ?? $this->currentLocale()][$name];
    }

    protected function operationFinished()
    {
        $this->next_operation = [];
    }

    /**
     * Get FlexProperty value.
     *
     * @param string $name
     *
     * @throws FlexPropertyException
     *
     * @return mixed
     */
    public function getFlexPropertyValue(string $name)
    {
        return optional($this->getFlexProperty($name))->value;
    }

    /**
     * @param string $name
     *
     * @throws FlexPropertyException
     *
     * @return mixed
     */
    public function getFlexProperty(string $name)
    {
        $this->hasFlexPropertyOrFail($name);
        $locale = $this->currentLocale();

        if (! $this->hasFlexObject($name, $locale)) {
            $property = $this->getFlexPropertyFromDb($name, $locale);
            $this->flex_objects[$locale][$name] = $property ?? $this->makeFlexObject($name, $locale);
        }

        return $this->flexPropertyReference($name, $locale);
    }

    public function hasFlexObject($name, $locale = null)
    {
        return isset($this->flex_objects[$locale ?? $this->currentLocale()][$name]);
    }

    /**
     * Get flex property value from database.
     *
     * @param string      $name
     * @param string|null $locale
     *
     * @throws FlexPropertyException
     *
     * @return FlexProperty
     */
    protected function getFlexPropertyFromDb(string $name, string $locale = null)
    {
        return Flex::factory($this->getFlexPropertyType($name))
            ->where('linkable_type', static::class)
            ->where('linkable_id', $this->{'id'})
            ->where('name', $name)
            ->where('locale', $locale ?? $this->currentLocale())
            ->first();
    }

    /**
     * Set FlexProperty value.
     *
     * @param string $name
     * @param $value
     *
     * @throws FlexPropertyException
     *
     * @return HasFlexProperties
     */
    public function setFlexPropertyValue(string $name, $values = null): self
    {
        $this->hasFlexPropertyOrFail($name);

        if ($this->currentLocale()) {
            $values = [$this->currentLocale() => $values];
        }

        foreach ($values as $locale=>$value) {
            if (! $this->hasFlexObject($name, $locale)) {
                $this->flex_objects[$locale][$name] = $this->makeFlexObject($name, $locale);
            }
            $this->flexPropertyReference($name, $locale)->value = $value;
        }

        return $this;
    }

    /**
     * Make flex property object.
     *
     * @param null  $name
     * @param null  $locale
     * @param array $attributes
     *
     * @throws FlexPropertyException
     *
     * @return FlexProperty
     */
    protected function makeFlexObject($name = null, $locale = null, $attributes = [])
    {
        $attributes = array_merge(
            $attributes,
            ['name' => $name, 'locale' => $locale ? $locale : $this->currentLocale()]
        );

        return Flex::factory($this->getFlexPropertyType($name), $attributes);
    }

    /**
     * Set the current locale.
     *
     * @param string|null $locale
     *
     * @return $this
     */
    public function locale(string $locale = null): self
    {
        static::$locale['current'] = $locale;

        return $this;
    }

    public function nextLocale(string $locale = null)
    {
        $this->next_operation['locale'] = $locale;

        return $this;
    }

    public function nextOperation(string $method, $arguments = [], $forward = false)
    {
        $on = $forward ? 'forward' : 'self';
        $this->next_operation[$on][$method] = $arguments;

        return $this;
    }

    /**
     * Return the current flex property locale.
     *
     * @return string
     */
    protected function currentLocale(): ?string
    {
        return isset($this->next_operation['locale'])
            ? $this->next_operation['locale']
            : static::$locale['current'];
    }

    /**
     * Store all flex property values.
     *
     * @see bootHasFlexProperties
     *
     * @return $this
     */
    public function storeFlexProperties(): self
    {
        collect($this->flex_objects)->flatten()->each(function ($property) {
            $property->forceFill([
                'linkable_type' => static::class,
                'linkable_id'   => $this->{'id'},
            ])->save();
        });

        return $this;
    }

    /**
     * Load all FlexProperty values from persistence.
     *
     * @see bootHasFlexProperties
     *
     * @return $this
     */
    public function loadFlexProperties()
    {
        collect($this->flex_properties)->flip()->map(function ($value, $type) {
            return Flex::factory($type)
                    ->where('linkable_id', $this->{'id'})
                    ->where('linkable_type', static::class)
                    ->get();
        })
            ->filter()
            ->flatten()
            ->each(function ($property) {
                $this->flex_objects[$property->locale][$property->name] = $property;
            });

        return $this;
    }

    public function reloadFlexProperties()
    {
        return $this->loadFlexProperties();
    }

    /**
     * Get a reference to flex property object.
     *
     * @param string $name
     *
     * @throws FlexPropertyException
     *
     * @return FlexProperty
     */
    public function flex(string $name)
    {
        return $this->getFlexProperty(
            $this->hasFlexPropertyOrFail($name)
        );
    }

    public function flexWhere($name, $operator, $value = null)
    {
        $type = $this->getFlexPropertyType(
            $this->hasFlexPropertyOrFail($name)
        );

        if (is_null($value)) {
            $value = $operator;
            $operator = '=';
        }

        return function ($query) use ($type, $operator, $value, $name) {
            $query->where('flex_tbl_'.$type.'.value', $value);
            $query->where('flex_tbl_'.$type.'.name', $name);

            return $query;
        };
    }
}
