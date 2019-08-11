<?php

namespace Laramate\FlexProperties\Types;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Config;
use Laramate\FlexProperties\Flex;

abstract class FlexProperty extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'locale',
        'value',
    ];

    /**
     * @return MorphTo
     */
    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTypeAttribute()
    {
        $config = Config::get('flex-properties.types');

        return Collect($config)->flip()->get(static::class);
    }

    public function getLinkableKeyAttribute()
    {
        return 'flex_' . $this->type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return is_scalar($this->attributes['value']) ? (string) $this->attributes['value'] : '';
    }
}