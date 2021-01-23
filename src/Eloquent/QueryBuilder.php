<?php


namespace Golly\Authority\Eloquent;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class QueryBuilder
 * @package Golly\Authority\Eloquent
 */
class QueryBuilder extends Builder
{


    /**
     * @param string $column
     * @param $value
     * @return QueryBuilder
     */
    public function whereLeftLike(string $column, $value)
    {
        return $this->where($column, 'LIKE', "$value%");
    }

    /**
     * @param string $column
     * @param $value
     * @return QueryBuilder
     */
    public function orWhereLeftLike(string $column, $value)
    {
        return $this->orWhere($column, 'LIKE', "$value%");
    }

    /**
     * @param string $column
     * @param $value
     * @return QueryBuilder
     */
    public function whereLike(string $column, $value)
    {
        return $this->where($column, 'LIKE', "%$value%");
    }

    /**
     * @param string $column
     * @param $value
     * @return QueryBuilder
     */
    public function orWhereLike(string $column, $value)
    {
        return $this->orWhere($column, 'LIKE', "%$value%");
    }

    /**
     * @param string $field
     * @param Carbon $carbon
     * @return QueryBuilder
     */
    public function whereBefore(string $field, Carbon $carbon)
    {
        return $this->where($field, '<=', $carbon);
    }


    /**
     * @param string $field
     * @param Carbon $carbon
     * @return QueryBuilder
     */
    public function orWhereBefore(string $field, Carbon $carbon)
    {
        return $this->orWhere($field, '<=', $carbon);
    }

    /**
     * @param string $field
     * @param Carbon $carbon
     * @return QueryBuilder
     */
    public function whereAfter(string $field, Carbon $carbon)
    {
        return $this->where($field, '<=', $carbon);
    }

    /**
     * @param string $field
     * @param Carbon $carbon
     * @return QueryBuilder
     */
    public function orWhereAfter(string $field, Carbon $carbon)
    {
        return $this->orWhere($field, '<=', $carbon);
    }
}
