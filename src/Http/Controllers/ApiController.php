<?php


namespace Golly\Authority\Http\Controllers;


use App\Http\Controllers\Controller;
use Exception;
use Golly\Authority\Eloquent\Model;
use Golly\Authority\Exceptions\ModelUndefinedException;
use Golly\Authority\Http\Resources\ResourceCollection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ApiController
 * @package Golly\Authority\Http\Controllers
 */
class ApiController extends Controller
{
    use DispatchesJobs;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @return JsonResponse
     */
    public function version()
    {
        return $this->sendArray([
            'version' => app()->version()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ModelUndefinedException
     */
    public function index(Request $request)
    {
        $paginator = $this->makeModel()
            ->paginate($request->query());

        return $this->sendPaginator($paginator);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ModelUndefinedException
     */
    public function show(Request $request, $id)
    {
        $item = $this->makeModel()
            ->filter($request->query())
            ->findOrFail($id);

        return $this->sendItem($item);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id)
    {
        $item = $this->makeModel()->findOrFail($id);
        $item->delete();

        return $this->sendNoContent();
    }

    /**
     * @return Model
     * @throws ModelUndefinedException
     */
    protected function makeModel()
    {
        if ($this->modelClass && class_exists($this->modelClass)) {
            $model = new $this->modelClass;
            if ($model instanceof Model) {
                return $model;
            }
        }

        throw new ModelUndefinedException();
    }

    /**
     * @param $resource
     * @param string|null $collects
     * @return JsonResponse
     */
    protected function sendPaginator($resource, string $collects = null)
    {
        return (new ResourceCollection($resource, $collects))->response();
    }

    /**
     * @param $resource
     * @param string|null $collects
     * @return JsonResponse
     */
    protected function sendCollection($resource, string $collects = null)
    {
        return (new ResourceCollection($resource, $collects))->response();
    }


    /**
     * @param $resource
     * @return JsonResponse
     */
    protected function sendItem($resource)
    {
        if ($resource instanceof JsonResource) {
            return $resource->response();
        }

        return (new JsonResource($resource))->response();
    }


    /**
     * @param array $data
     * @return JsonResponse
     */
    protected function sendArray(array $data)
    {
        return response()->json(['data' => $data]);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    protected function sendMessage(string $message)
    {
        return response()->json(['message' => $message]);
    }

    /**
     * @param int $status
     * @return JsonResponse
     */
    protected function sendNoContent($status = 204)
    {
        return response()->json([])->setStatusCode($status);
    }
}
