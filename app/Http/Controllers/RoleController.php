<?php

namespace App\Http\Controllers;

use App\Model\OperatorLogModel;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Role;
use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\RoleCreateRequest;
use App\Http\Requests\RoleUpdateRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->currentPath= Route::getFacadeRoot()->current()->uri();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // get current user role data
        $user = $request->user();
        $currentRoleData = Role::getUserRoleData($user->id);
        // get current user role and direct children roles
        $current_role_id = $currentRoleData->role_id;
        $sql = "parent_id=$current_role_id";
        if ($current_role_id == 1) {
            $sql = "id=$current_role_id or parent_id=$current_role_id";
        }
        $roles = Role::whereRaw($sql)->latest()->paginate($request->get('limit',10));
//        $roles = Role::whereIn('id', $roleIds)->latest()->paginate($request->get('limit',10));
        foreach ($roles as $item) {
            $item->parent_name = 'No Parent Role';
            if ($item->parent_id > 0) {
                $item->parent_name = Role::select(['display_name'])->where('id', $item->parent_id)->value('display_name');
            }
        }
        return View::make('role.index',compact('roles','current_role_id'));
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function create(Request $request){
        // get current user role data
        $user = $request->user();
        $currentRoleData = Role::getUserRoleData($user->id);
        // get current user role and children roles
//        $roles = Role::getChildrenRoles($roleData->parent_id,$roleData->role_id);
        $role_id = $currentRoleData->role_id;
        return View::make('role.create',compact('currentRoleData','role_id'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleCreateRequest $request)
    {
        // add operator log
        OperatorLogModel::insertLog($request, 'Created','Role','Attempted create role [name]=['.$request->name.']');
        if( Role::create($request->only(['name','display_name','descriptions','parent_id'])) ) {
            Session::flash('alert-success', 'Role Added.');
        }
        else{
            Session::flash('alert-danger', 'Role Added filed.');
        }

        return Redirect::to(URL::route('role.index'));
    }

    /**
     * Edit role page view.
     * @author Evans <evans.yang@greenpacket.com.cn>
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    public function edit(Request $request,$id){
        // get current user role data
        $user = $request->user();
        $currentRoleData = Role::getUserRoleData($user->id);
        // get choose role data
        $role=Role::findOrFail($id);
        // get choose role's parent name
        $parent_name = 'No Parent Role';
        if ($role->parent_id > 0) {
            $parent_name = Role::select('display_name')->where('id',$role->parent_id)->value('display_name');
        }
        $role->parent_name = $parent_name;
        // get current user role and children roles
//        $roles = Role::getAllChildrenRoles($request, true);
        return View::make('role.edit',compact('role','currentRoleData'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleUpdateRequest $request, $id)
    {
        // add operator log
        OperatorLogModel::insertLog($request, 'Updated','Role','Attempted update role [name]=['.$request->name.']');
        $role = Role::findOrFail($id);

        $data = $request->only(['name','display_name','descriptions']);
        if($role->update($data)) {
            Session::flash('alert-success',"Role `{$role->name}` has been updated.");
        } else {
            Session::flash('alert-danger',  "Role with id {$id} note found.");
        }

        return redirect()->route('role.index');
    }

    /**
     * Assign direct permission page display
     * @author Evans <evans.yang@greenpacket.com.cn>
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function permission_old($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::with(['allChilds'=>function($query){
            return $query->where(['discarded'=>0]);
        }])->where(['parent_id'=>0,'discarded'=>0])->get();
        foreach ($permissions as $k=>$p1){
            $p1->own = $role->hasDirectPermission($p1->id) ? 'checked' : '' ;
            if ($p1->allChilds->isNotEmpty()){
                foreach ($p1->allChilds as $p2){
                    $p2->own = $role->hasDirectPermission($p2->id) ? 'checked' : '' ;
                    if ($p2->allChilds->isNotEmpty()){
                        foreach ($p2->allChilds as $p3){
                            $p3->own = $role->hasDirectPermission($p3->id) ? 'checked' : '' ;
                        }
                    }
                }
            }
        }
        return View::make('role.permission',compact('role','permissions'));
    }

    /**
     * @Desc Assign direct permission page display
     * @Author <jh.rao@greenpacket.com.cn>
     * @Time 2020/7/29 10:35
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function permission(Request $request,$id)
    {
        // get current user role data
        $user = $request->user();
        $roleData = Role::getUserRoleData($user->id);
        $role_id = $roleData->role_id;
        $current_role = Role::findOrFail($role_id);
        // get choose role data
        $role = Role::findOrFail($id);
        // get all permissions
        $permissions_obj = Permission::with(['allChilds'=>function($query){
            return $query->where(['discarded'=>0]);
        }])->where(['parent_id'=>0,'discarded'=>0])->get();
        $permissions = [];
        $i = 0;
        foreach ($permissions_obj as $k=>$p1){
            // if current user dont have this permissions will not record to $permissions
            if ($role_id != 1 && $id != 1 && !$current_role->hasDirectPermission($p1->id)) {
                $i++;
                continue;
            }
            // if choose role have this permissions will mark as checked
            $p1->own = $role->hasDirectPermission($p1->id) ? 'checked' : '' ;
            if ($p1->allChilds->isNotEmpty()){
                foreach ($p1->allChilds as $p2_k=>$p2){
                    // if current user dont have this permissions will not record to $permissions
                    if ($role_id != 1 && $id != 1 && !$current_role->hasDirectPermission($p2->id)) {
                        $i++;
                        unset($p1->allChilds[$p2_k]);
                        continue;
                    }
                    // if choose role have this permissions will mark as checked
                    $p2->own = $role->hasDirectPermission($p2->id) ? 'checked' : '' ;
                    if ($p2->allChilds->isNotEmpty()){
                        foreach ($p2->allChilds as $p3_k=>$p3){
                            // if current user dont have this permissions will not record to $permissions
                            if ($role_id != 1 && $id != 1 && !$current_role->hasDirectPermission($p3->id)) {
                                $i++;
                                unset($p2->allChilds[$p3_k]);
                                continue;
                            }
                            // if choose role have this permissions will mark as checked
                            $p3->own = $role->hasDirectPermission($p3->id) ? 'checked' : '' ;
                        }
                    }
                }
            }
            $permissions[$i] = $p1;
            $i++;
        }
//        echo "<pre>";
//        print_r($permissions);exit;
//        dd($permissions);
        return View::make('role.permission',compact('role','permissions'));
    }

    /**
     * @Desc Get the selected role and the corresponding permissions
     * @Author <jh.rao@greenpacket.com.cn>
     * @Time 2020/7/20 10:58
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function permission_parent_test($id)
    {
        $user = Auth::user();
        $role = Role::findOrFail($id);
        // get parent role
        $parent_role_id = $role->parent_id;
        if ($parent_role_id > 0) {
            $parent_role = Role::find($parent_role_id);
        }
        $permissions_obj = Permission::with(['allChilds'=>function($query){
            return $query->where(['discarded'=>0]);
        }])->where(['parent_id'=>0,'discarded'=>0])->get();
        $permissions = [];
        foreach ($permissions_obj as $k=>$p1){
            if ($parent_role_id > 0) {
                if (!$parent_role->hasDirectPermission($p1->id)) {
                    continue;
                }
            }
//            $p1->own = $user->can($p1->name) ? 'checked' : '' ;
            $p1->own = $role->hasDirectPermission($p1->id) ? 'checked' : '' ;
            if ($p1->allChilds->isNotEmpty()){
                foreach ($p1->allChilds as $p2){
//                    $p2->own = $user->can($p2->name) ? 'checked' : '' ;
                    $p2->own = $role->hasDirectPermission($p2->id) ? 'checked' : '' ;
                    if ($p2->allChilds->isNotEmpty()){
                        foreach ($p2->allChilds as $p3){
//                            $p3->own = $user->can($p3->name) ? 'checked' : '' ;
                            $p3->own = $role->hasDirectPermission($p3->id) ? 'checked' : '' ;
                        }
                    }
                }
            }
            $permissions[$k] = $p1;
        }
        return View::make('role.permission',compact('role','permissions'));
    }

    /**
     * Assign direct permission action
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignPermission(Request $request,$id)
    {
        // get current user role
        $user = $request->user();
        $currentRoleData = Role::getUserRoleData($user->id);
        $current_role_id = $currentRoleData->role_id;
        if ($current_role_id == $id) {
            Session::flash('alert-danger', "No access to assign permissions.");
            return Redirect::back();
        }

        // add operator log
        OperatorLogModel::insertLog($request, 'AssignPermission','Role','Attempted assign permission to role [id]=['.$id.']');
        $role = Role::findOrFail($id);
        $permissions = $request->get('permissions',[]);
        try{
            $role->syncPermissions($permissions);
            Session::flash('alert-success', "Role `{$role->name}`  permissions has been updated.");
            return Redirect::to(URL::route('role.index'));
        }catch (\Exception $exception){
            Session::flash('alert-danger', "This role `{$role->name}` unable to assign permissions.");
            return Redirect::back();
        }
    }

    /**
     * Delete Roles
     * @author Evans <evans.yang@greenpacket.com.cn>
     * @param Request $request
     * @return void
     */
    public function destroy(Request $request)
    {
        $id = $request->get('id');
        // add operator log
        OperatorLogModel::insertLog($request, 'Deleted','Role','Attempted delete role [id]=['.$id.']');
        if (empty($id)){
            return Response::json(['code'=>1,'message'=>'Please select delete item']);
        }

        $roleData = Role::getUserRoleData(Auth::user()->id);
        if ( $roleData->role_id == $id ) {
            return [
                'code' => 1,
                'message' => 'Deletion of currently logged in role is not allowed.',
            ];
        }

        $roles = Role::with('childs')->find($id);
        if ($roles->childs->isNotEmpty()) {
            return Response::json([
                'code' => 1,
                'message' => 'Parent role cannot be deleted.',
            ]);
        }

        $role = Role::findOrFail($id);
        if ($role->parent_id <= 0) {
            return Response::json(['code' => 1,'message' => 'Cannot delete super admin role.']);
        }

        if($role->delete()){
            return Response::json(['code'=>0,'message'=>'Role has been deleted.']);
        }else{
            return Response::json(['code'=>1,'message'=>'Unable to delete role.']);
        }
    }
}
