<?php

namespace App\Filters;

class IOT_DeviceFilter extends QueryFilter
{
    protected $filterable = [
        'id',
        'home_id',
        'ip',
        'air_val',
        'left_status',
        'right_status',
        'status',
    ];

    public function filterName($name)
    {
        return $this->builder->where('name', 'like', '%' . $name . '%');
    }
}

