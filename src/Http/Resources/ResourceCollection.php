<?php


namespace Golly\Authority\Http\Resources;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection as IlluminateResourceCollection;

/**
 * Class ResourceCollection
 * @package Golly\Authority\Http\Resources
 */
class ResourceCollection extends IlluminateResourceCollection
{

    /**
     * ResourceCollection constructor.
     * @param $resource
     * @param string|null $collects
     */
    public function __construct($resource, string $collects = null)
    {
        $this->collects = $collects;

        parent::__construct($resource);
    }

    /**
     * Create a paginate-aware HTTP response.
     *
     * @param Request $request
     * @return JsonResponse
     */
    protected function preparePaginatedResponse($request)
    {
        if ($this->preserveAllQueryParameters) {
            $this->resource->appends($request->query());
        } elseif (!is_null($this->queryParameters)) {
            $this->resource->appends($this->queryParameters);
        }

        return (new PaginatedResourceResponse($this))->toResponse($request);
    }
}
