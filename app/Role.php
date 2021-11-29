<?php

namespace App;

use App\Permission;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Role extends \Spatie\Permission\Models\Role
{
    use SoftDeletes;
    public static function defaultRoles()
    {
        return [
            [
                'name' => 'Root',
                'display_name' => 'Super Administrator',
                'descriptions' => 'Super administrator has all permissions',
                'guard_name' => 'web',
                'default_permissions' => [
                     "system",
                     "system.user",
                     "system.user.create",
                     "system.user.edit",
                     "system.user.destroy",
                     "system.user.role",
                     "system.user.permission",
                     "system.roles",
                     "system.role.create",
                     "system.role.edit",
                     "system.role.destroy",
                     "system.role.permission",
                     "system.permissions",
                     "system.permission.create",
                     "system.permission.edit",
                     "system.permission.destroy",
//                     "system.config.site",
//                     "system.config.site.update",
                     "system.session_logs"
                ]
            ],
            [
                'name' => 'Admin',
                'display_name' => 'Administrator',
                'descriptions' => '',
                'guard_name' => 'web',
                'default_permissions' => [
                     "system.session_logs",
                ]
            ],

        ];
    }


    public function childs()
    {
        return $this->hasMany('App\Role','parent_id','id');
    }


    public function children(){
        return $this->childs()->with(['children'=>function($query){
            $query->select(['id','parent_id','name','display_name']);
        }]);
    }


    public static function getChildrenRoles($parentId=0, $id=0, $is_get_parent_role=false)
    {
        if ($id > 1) {
            $roles = [];
            $sql = "id=$id or parent_id=$id";
            if ($is_get_parent_role && $parentId > 0) {
                $sql .= " or id=$parentId";
            }
            $res = Role::with(['children'=>function($query){
                $query->select(['id','parent_id','name','display_name']);
            }])->select(['id','parent_id','name','display_name'])
                ->whereRaw($sql)
                ->get()->toArray();
            if (!empty($res)) {
                foreach ($res as $k=>$item) {
                    if ($k > 0) {
                        break;
                    }
                    $i = 1;
                    $j = 0;
                    // deal the childrens data to $roles array
                    array_walk_recursive($item,function ($v) use(&$roles, &$i, &$j){
                        $field = 'id';
                        if ($i == 2) {
                            $field = 'parent_id';
                        } elseif ($i == 3) {
                            $field = 'name';
                        } elseif ($i == 4) {
                            $field = 'display_name';
                        }
                        $roles[$j][$field]=$v;
                        if ($i%4==0) {
                            $j ++;
                            $i = 0;
                        }
                        $i ++;
                    });
                }

            }
        } else {
            $roles = Role::select(['id','parent_id','name','display_name'])->get()->toArray();
        }
        return $roles;
    }


    public static function getUserRoleData($userId)
    {
        return DB::table('model_has_roles')
            ->join('roles','model_has_roles.role_id','=','roles.id')
            ->select(['role_id','parent_id','display_name'])
            ->where('model_id',$userId)
            ->where('model_type','App\User')
            ->where('deleted_at',null)
            ->first();
    }


    public static function getAllChildrenRoles(Request $request, $is_get_parent_role=false)
    {
        // get current user role data
        $user = $request->user();
        $roleData = self::getUserRoleData($user->id);
        // get current user role and children roles
        return self::getChildrenRoles($roleData->parent_id,$roleData->role_id,$is_get_parent_role);
    }


    public static function getDirectChildrenRolesData(Request $request)
    {
        // get current user role data
        $user = $request->user();
        $roleData = self::getUserRoleData($user->id);
        return self::getDirectChildrenRoles($roleData->role_id);
    }


    public static function getDirectChildrenRoles($id)
    {
        return Role::select(['id','parent_id','name','display_name'])->whereRaw("id=$id or parent_id=$id")->get()->toArray();
    }


    public static function onlyGetDirectChildrenRoles($id)
    {
        return Role::select(['id','parent_id','name','display_name'])->whereRaw("parent_id=$id")->get()->toArray();
    }


}
