<?php

namespace App\Filters;

use App\Filters\QueryFilter;

class LocationFilter extends QueryFilter
{
    protected $filterable = array (
  0 => 'longitude',
  1 => 'latitude',
  2 => 'id',
);

    // Example filter method
    public function filterName($name)
    {
        return $this->builder->where('name', 'like', '%' . $name . '%');
    }
}
