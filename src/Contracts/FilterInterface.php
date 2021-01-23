<?php


namespace Golly\Authority\Contracts;


use Golly\Authority\Eloquent\QueryBuilder;

/**
 * Interface FilterInterface
 * @package Golly\Authority\Contracts
 */
interface FilterInterface
{

    /**
     * @param QueryBuilder $query
     * @param array $params
     * @return QueryBuilder
     */
    public function handle(QueryBuilder $query, array $params): QueryBuilder;
}
