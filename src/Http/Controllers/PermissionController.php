<?php


namespace Golly\Authority\Http\Controllers;


use Golly\Authority\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class PermissionController
 * @package Golly\Authority\Http\Controllers
 */
class PermissionController extends ApiController
{

    /**
     * @var string
     */
    protected $modelClass = Permission::class;

    /**
     * @var string[]
     */
    protected $attributes = [
        'name' => '权限',
        'description' => '描述信息'
    ];

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:permissions',
            'description' => 'nullable|string',
        ], [], $this->attributes);

        $permission = (new Permission())->create($data);

        return $this->sendItem($permission);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $permission = (new Permission())->findOrFail($id);
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('permissions', 'name')->ignore($id)
            ],
            'description' => 'nullable|string',
        ], [], $this->attributes);
        $permission->fill($data)->save();

        return $this->sendItem($permission);
    }

}
