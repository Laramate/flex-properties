<?php

namespace Laramate\FlexProperties\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Laramate\FlexProperties\Exceptions\FlexPropertyException;
use Laramate\FlexProperties\Flex;
use Laramate\FlexProperties\Interfaces\FlexProperty;
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
    protected $flex_joins;
    protected $flex_properties;

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
    }

    public function scopeFlexProperties($query)
    {
        collect($this->flex_properties)->flip()->each(function ($item, $type) use (&$query) {
            $property = Flex::factory($type);
            $tableAlias = 'flex_tbl_'.$type;
            $query->leftJoin("'".$property->getTable()."'".' AS '.$tableAlias, function ($join) use ($type, $tableAlias) {
                $join->on($tableAlias.'.linkable_id', "'".$this->getTable()."'".'.id');
                $join->where($tableAlias.'.linkable_type', "'".static::class."'");
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
        return array_key_exists($name, $this->getFlexProperties());
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
     * Return a reference to a flex property object.
     */
    protected function &getFlexProperty(string $name)
    {
        return $this->getFlexProperties()[$name];
    }

    protected function &getFlexProperties(): array
    {
        if (empty($this->flex_properties)) {
            $this->flex_properties = $this->composeFlexProperties();
        }

        return $this->flex_properties;
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
        return $this->getFlexProperty($name)->getValue();
    }

    protected function composeFlexProperties(): array
    {
        return Collection::make($this->flexProperties())
            ->mapWithKeys(function ($property) {
                return [$property->name => $property->attach($this)];
            })
            ->toArray();
    }

    protected function queryFlexPropertyValues()
    {
        $query = $this->newQuery();
    }

    protected function addFlexPropertyJoin(Builder &$query, FlexProperty &$flexProperty)
    {
        $table = $flexProperty->getTable();

        return $query->join($table, function (Builder $join) use ($table) {
            return $join->on($table.'.linkable_id', '=', $this->getTable().'.id')
                ->where('linkable_type', static::class);
        });
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

        $this->getFlexProperty($name)->setValue($values);

        /*
        foreach ($values as $locale=>$value) {
            if (! $this->hasFlexObject($name, $locale)) {
                $this->flex_objects[$locale][$name] = $this->makeFlexObject($name, $locale);
            }
            $this->flexPropertyReference($name, $locale)->value = $value;
        }
        */

        return $this;
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

    /**
     * Return the current flex property locale.
     *
     * @return string
     */
    protected function currentLocale(): ?string
    {
        return static::$locale['current'];
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
