<?php

namespace App\Http\Controllers;

use App\Model\OperatorLogModel;
use App\Role;
use Illuminate\Support\Facades\DB;
use Session;
use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\PermissionCreateRequest;
use App\Http\Requests\PermissionUpdateRequest;

class PermissionController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->currentPath= Route::getFacadeRoot()->current()->uri();
    }

    /**
     * get permission list
     * @author Evans <evans.yang@greenpacket.com.cn>
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // get current user role data
        $user = $request->user();
        $roleData = Role::getUserRoleData($user->id);
        $role_id = $roleData->role_id;
        $premissions = Permission::select(['id','type','name','display_name','route_name','descriptions','discarded'])
            ->where('parent_id',0)->orderBy('sort')->where('discarded',0)->paginate($request->get('limit', 10));
        return View::make('permission.index',compact('premissions','role_id'));
    }

    /**
     * Undocumented function
     * @author Evans <evans.yang@greenpacket.com.cn>
     * @param Request $request
     * @return void
     */
    public function allRecords(Request $request){
        $premissions = Permission::with(['children'=>function($query){
            $query->orderBy('sort');
        }])->select(['id','type','name','display_name','route_name','descriptions','discarded'])
            ->where('parent_id',0)->orderBy('sort')->get();

        return Response::json([
            'code' => 0,
			'message' => 'success',
			'data' => $premissions
        ]);
    }

    /**
     * Create a new permission view
     * @author Evans <evans.yang@greenpacket.com.cn>
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $permissions = Permission::with(['childs'=>function($query){
            $query->select(['id','parent_id','display_name'])->where(['discarded'=>0,'type'=>2]);
        }])->select(['id','parent_id','display_name'])
        ->where(['parent_id'=>0,'discarded'=>0,'type'=>2])->get();
        return View::make('permission.create', compact('permissions'));
    }

    /**
     * Create a new permission action
     * @author Evans <evans.yang@greenpacket.com.cn>
     * @param PermissionCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PermissionCreateRequest $request)
    {
        // add operator log
        OperatorLogModel::insertLog($request, 'Created','Permission','Attempted create permission [DisplayName]=['.$request->display_name.']');
        $user = $request->user();
        $roleData = Role::getUserRoleData($user->id);
        $role_id = $roleData->role_id;
        if ($role_id != 1) {
            Session::flash('alert-warning', 'No access to create permission.');
            return Redirect::back();
        }
        $data = $request->all();
        try {
            Permission::create($data);
            Session::flash('alert-success', 'Permission has been created.');
            return Redirect::route('permissions');
        } catch (\Exception $exception) {
            Log::error("Create permission faild.",[$exception]);
            Session::flash('alert-warning', 'Unable to create permission.');
            return Redirect::back();
        }

    }

    /**
     * update permission page view
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(int $id)
    {
        $permission = Permission::findOrFail($id);
        $permissions = Permission::with(['childs'=>function($query){
            $query->select(['id','parent_id','display_name'])->where(['discarded'=>0,'type'=>2]);
        }])->select(['id','parent_id','display_name'])
        ->where(['parent_id'=>0,'discarded'=>0,'type'=>2])->get();
        return View::make('permission.edit', compact('permission', 'permissions'));
    }

    /**
     * update permission action
     * @param PermissionUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PermissionUpdateRequest $request, $id)
    {
        // add operator log
        OperatorLogModel::insertLog($request, 'Updated','Permission','Attempted update permission [DisplayName]=['.$request->display_name.']');
        $user = $request->user();
        $roleData = Role::getUserRoleData($user->id);
        $role_id = $roleData->role_id;
        if ($role_id != 1) {
            Session::flash('alert-warning', 'No access to update permission.');
            return Redirect::back();
        }
        $permission = Permission::findOrFail($id);
        $data = $request->all();
        try {
            $permission->update($data);
            Session::flash("alert-success", "Permission has been updated.");
            return Redirect::route('permissions');
        } catch (\Exception $exception) {
            Session::flash("alert-warning", "Unable to updated permission.");
            return Redirect::back();
        }
    }

    /**
     * Delete Permission
     * @author Evans <evans.yang@greenpacket.com.cn>
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->get('id');
        // add operator log
        OperatorLogModel::insertLog($request, 'Disabled','Permission','Attempted disable permission [id]=['.$id.']');
        if (empty($id)) {
            return  Response::json([
                'code' => 1,
				'message' => 'Please choose the item you want to disable.',
            ]);
        }

        $user = $request->user();
        $roleData = Role::getUserRoleData($user->id);
        $role_id = $roleData->role_id;
        if ($role_id != 1) {
            return Response::json([
                'code' => 1,
                'message' => 'No access to disable permission.',
            ]);
        }

        $permission = Permission::with('childs')->find($id);
        if (!$permission) {
            return Response::json([
                'code' => 1,
				'message' => 'The item chosen for disable does not exist.',
            ]);
        }

        //If there are sub-rights, delete is prohibited
        if ($permission->childs->isNotEmpty()) {
            return Response::json(['code' => 1, 'message' => 'Parent permission cannot be disabled']);
        }

        try {
            $permission->discarded=1;
            if($permission->save()){
                DB::table('model_has_permissions')->where(['permission_id'=>$permission->id])->delete();
                DB::table('role_has_permissions')->where(['permission_id'=>$permission->id])->delete();
                return Response::json(['code' => 0, 'message' => 'Permission has been disable']);
            }
        } catch (\Exception $exception) {
            return Response::json(['code' => 1, 'message' => 'Parent permission cannot be disabled']);
        }
    }

    /**
     * @Desc recovered permission
     * @Author <andy.dai@greenpacket.com.cn>
     * @Time 2019/11/15 11:52
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recovered(Request $request){
        $id = $request->get('id');
        // add operator log
        OperatorLogModel::insertLog($request, 'Recovered','Permission','Attempted recover permission [id]=['.$id.']');
        if (empty($id)) {
            return  Response::json([
                'code' => 1,
                'message' => 'Please choose the item you want to recover.',
            ]);
        }

        $user = $request->user();
        $roleData = Role::getUserRoleData($user->id);
        $role_id = $roleData->role_id;
        if ($role_id != 1) {
            return Response::json([
                'code' => 1,
                'message' => 'No access to recover permission.',
            ]);
        }

        $permission = Permission::find($id);
        if (!$permission) {
            return Response::json([
                'code' => 1,
                'message' => 'The item chosen for recover does not exist.',
            ]);
        }

        try {
            $permission->discarded=0;
            if($permission->save()){;

                return Response::json(['code' => 0, 'message' => 'Permission has been recovered.']);
            }
        } catch (\Exception $exception) {
            return Response::json(['code' => 1, 'message' => 'failed.']);
        }

    }
}
