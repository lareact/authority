<?php


namespace Golly\Authority\Models\Filters;


use Golly\Authority\Eloquent\QueryBuilder;
use Golly\Authority\Eloquent\ModelFilter;

/**
 * Class UserFilter
 * @package Golly\Authority\Models\Filters
 */
class UserFilter extends ModelFilter
{

    /**
     * @var string[]
     */
    protected $indexes = [
        'email',
        'name'
    ];

    /**
     * @param string $value
     * @return QueryBuilder
     */
    public function email(string $value)
    {
        return $this->query->where('email', $value);
    }

    /**
     * @param string $value
     * @return QueryBuilder
     */
    public function name(string $value)
    {
        return $this->query->like('name', $value);
    }
}
