<?php

namespace Golly\Authority\Filters;


use Golly\Authority\Eloquent\Filter;

/**
 * Class UserFilter
 * @package Golly\Authority\Filters
 */
class UserFilter extends Filter
{

    /**
     * @var string[]
     */
    protected $indexes = [
        'email',
        'phone',
        'name'
    ];

    /**
     * @param string $value
     * @return void
     */
    public function name(string $value)
    {
        $this->query->whereLike('name', $value);
    }

    /**
     * @param string $value
     * @return void
     */
    public function email(string $value)
    {
        $this->query->where('email', $value);
    }

    /**
     * @param string $value
     * @return void
     */
    public function phone(string $value)
    {
        $this->query->where('phone', $value);
    }
}
