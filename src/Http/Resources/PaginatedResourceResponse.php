<?php


namespace Golly\Authority\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse as IlluminatePaginatedResourceResponse;
use Illuminate\Support\Arr;

/**
 * Class PaginatedResourceResponse
 * @package Golly\Authority\Http\Resources
 */
class PaginatedResourceResponse extends IlluminatePaginatedResourceResponse
{

    /**
     * @param Request $request
     * @return array
     */
    protected function paginationInformation($request)
    {
        $paginated = $this->resource->resource->toArray();

        return [
            'page' => Arr::get($paginated, 'current_page', 1),
            'total' => Arr::get($paginated, 'total', 0),
            'meta' => $this->antMeta($paginated),
        ];
    }

    /**
     * @param $paginated
     * @return array
     */
    protected function antMeta($paginated)
    {
        return Arr::only($paginated, ['current_page', 'per_page', 'total', 'to', 'from']);
    }
}
