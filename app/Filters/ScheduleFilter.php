<?php

namespace App\Filters;

use App\Filters\QueryFilter;

class ScheduleFilter extends QueryFilter
{
    protected $filterable = array (
  0 => 'staff_id',
  1 => 'route_id',
  2 => 'start_time',
  3 => 'end_time',
);

    // Example filter method
    public function filterName($name)
    {
        return $this->builder->where('name', 'like', '%' . $name . '%');
    }
}
