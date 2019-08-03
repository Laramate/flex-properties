<?php

namespace Laramate\FlexProperties;

use Laramate\FlexProperties\Interfaces\FlexProperties;
use Laramate\FlexProperties\Traits\HasFlexProperties;
use Mindtwo\DynamicMutators\Traits\HasDynamicMutators;

class Model extends \Illuminate\Database\Eloquent\Model implements FlexProperties
{
    use HasDynamicMutators,
        HasFlexProperties;
}
