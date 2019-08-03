<?php

namespace Laramate\FlexProperties\Types;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    /**
     * @return string
     */
    public function __toString()
    {
        return is_scalar($this->attributes['value']) ? (string) $this->attributes['value'] : '';
    }
}