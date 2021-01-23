<?php


namespace Golly\Authority\Http\Controllers;


use Golly\Authority\Actions\CreateUserAction;
use Golly\Authority\Actions\UpdateUserAction;
use Golly\Authority\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class UserController
 * @package Golly\Authority\Http\Controllers
 */
class UserController extends ApiController
{
    /**
     * @var string
     */
    protected $modelClass = User::class;

    /**
     * @param Request $request
     * @param CreateUserAction $action
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request, CreateUserAction $action)
    {
        $user = $action->create($request->json());

        return $this->show($request, $user->id);
    }


    /**
     * @param Request $request
     * @param UpdateUserAction $action
     * @param $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, UpdateUserAction $action, $id)
    {
        $user = $action->update($id, $request->json());

        return $this->sendItem($user);
    }


    /**
     * @param Request $request
     * @param CreateUserAction $action
     * @param $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function storeChild(Request $request, CreateUserAction $action, $id)
    {
        $request->json()->add([
            'parent_id' => $id
        ]);

        $child = $action->create($request->json());

        return $this->show($request, $child->id);
    }


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function assignRoles(Request $request, $id)
    {
        $request->validate([
            'roles' => 'required|array'
        ], [], [
            'roles' => '角色'
        ]);
        $user = (new User())->findOrFail($id);
        $user->assignRoles($request->json('roles'));

        return $this->sendItem($user);
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
        $user = (new User())->findOrFail($id);
        $user->assignPermissions($request->json('permissions'));

        return $this->sendItem($user);
    }
}
