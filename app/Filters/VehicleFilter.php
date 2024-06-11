<?php

namespace App\Filters;

use App\Filters\QueryFilter;

class VehicleFilter extends QueryFilter
{
    protected $filterable = array (
  0 => 'license_plate',
  1 => 'status',
  2 => 'deleted',
);

    // Example filter method
    public function filterName($name)
    {
        return $this->builder->where('name', 'like', '%' . $name . '%');
    }
}
