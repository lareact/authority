<?php


namespace Golly\Authority\Models\Filters;


use Golly\Authority\Eloquent\QueryBuilder;
use Golly\Authority\Eloquent\ModelFilter;

/**
 * Class RoleFilter
 * @package Golly\Authority\Models\Filters
 */
class RoleFilter extends ModelFilter
{

    /**
     * @var string[]
     */
    protected $indexes = [
        'name'
    ];

    /**
     * @param $value
     * @return QueryBuilder
     */
    public function name($value)
    {
        return $this->query->like('name', $value);
    }
}
