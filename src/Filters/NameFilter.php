<?php

namespace Golly\Authority\Filters;


use Golly\Authority\Eloquent\Filter;

/**
 * Class PermissionFilter
 * @package Golly\Authority\Filters
 */
class NameFilter extends Filter
{

    /**
     * @var string[]
     */
    protected $indexes = [
        'name'
    ];

    /**
     * @param $value
     * @return void
     */
    public function name($value)
    {
        $this->query->whereLike('name', $value);
    }
}
