<?php

namespace App\Filters;

use App\Filters\QueryFilter;

class AddressFilter extends QueryFilter
{
    protected $filterable = array(
        0 => 'unit_number',
        1 => 'street_number',
        2 => 'address_line',
        3 => 'ward',
        4 => 'district',
        5 => 'city',
        6 => 'province',
        7 => 'country_name',
    );

    // Example filter method
    public function filterName($name)
    {
        return $this->builder->where('name', 'like', '%' . $name . '%');
    }
}
