<?php

namespace App\Filters;

use App\Filters\QueryFilter;

class UserFilter extends QueryFilter
{
    protected $filterable = array (
  0 => 'name',
  1 => 'email',
  2 => 'password',
);

    // Example filter method
    public function filterName($name)
    {
        return $this->builder->where('name', 'like', '%' . $name . '%');
    }
}
