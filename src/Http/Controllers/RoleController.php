<?php


namespace Golly\Authority\Http\Controllers;


use Golly\Authority\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class RoleController
 * @package Golly\Authority\Http\Controllers
 */
class RoleController extends ApiController
{

    /**
     * @var string
     */
    protected $modelClass = Role::class;

    /**
     * @var string[]
     */
    protected $attributes = [
        'name' => '角色',
        'description' => '描述信息'
    ];

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles',
            'description' => 'nullable|string',
        ], [], $this->attributes);
        $role = (new Role())->create($data);

        return $this->sendItem($role);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $role = (new Role())->findOrFail($id);
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('roles', 'name')->ignore($id)
            ],
            'description' => 'nullable|string',
        ], [], $this->attributes);
        $role->fill($data)->save();

        return $this->sendItem($role);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function assignPermissions(Request $request, $id)
    {
        $request->validate([
            'permissions' => 'required|array'
        ], [], [
            'permissions' => '权限'
        ]);
        $role = (new Role())->findOrFail($id);
        $role->assignPermissions($request->json('permissions'));

        return $this->sendItem($role);
    }

}
