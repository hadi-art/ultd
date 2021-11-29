<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class Permission extends \Spatie\Permission\Models\Permission
{
    use SoftDeletes;


    protected $appends = ['type_name','discard_name'];

    public function getTypeNameAttribute()
    {
        return $this->attributes['type_name'] = Arr::get([1=>'Menu',2=>'Button'],$this->type);
    }


    public function getDiscardNameAttribute()
    {
        return $this->attributes['discard_name'] = Arr::get([0=>'Able',1=>'Disable'],$this->discarded);
    }


    public function childs()
    {
        return $this->hasMany('App\Permission','parent_id','id');
    }


    public function allChilds()
    {
        return $this->childs()->with(['allChilds'=>function($query){
            return $query->where(['discarded'=>0]);
        }]);

    }


    public function children(){
        return $this->childs()->with('children');
    }

    public static function defaultPermissions()
    {
        return [
//            // Dashboard
//            [
//                'name'          => 'dashboard',
//                'type'          => 2,
//                'route_name'    => 'home',
//                'icons_name'    => 'zmdi-view-dashboard',
//                'display_name'  => 'Dashboard',
//                'descriptions'  => 'Ability to export relevant data reports',
//                'child' => []
//            ],
//            //Lane Dashboard
//            [
//                'name'          => 'lane_dashboard',
//                'type'          => 2,
//                'route_name'    => 'lane_dashboard',
//                'icons_name'    => 'zmdi-view-dashboard',
//                'display_name'  => 'Lane Dashboard',
//                'descriptions'  => 'Ability to export relevant data reports',
//                'child' => [
//                    ['name'=> 'lane_dashboard.change','type'=> 1,'route_name'=> 'lane_dashboard.print','icons_name'=> 'zmdi-delete','display_name'=> 'Change','descriptions'=> 'Ability to delete user records at "Users" page'],
//                    ['name'=> 'lane_dashboard.manually','type'=> 1,'route_name'=> 'lane_dashboard.export','icons_name'=> 'zmdi-', 'display_name'=> 'Manually Open Gate','descriptions'=> 'Ability to delete user records at "Users" page'],
//                ]
//            ],
//
//            // Analytic
//            [
//                'name'          => 'analytic',
//                'type'          => 2,
//                'route_name'    => 'javascript:void(0);',
//                'icons_name'    => 'zmdi-chart',
//                'display_name'  => 'Analytic',
//                'descriptions'  => 'Ability to export relevant data reports',
//                'child' => [
//                    [
//                        'name'=> 'analytic.entry_log',
//                        'type'=> 2,
//                        'route_name'=> 'entry_log',
//                        'icons_name'=> 'zmdi-file-tex',
//                        'display_name'=> 'Entry Logs',
//                        'descriptions'  => 'Ability to export relevant data reports',
//                        'child' => [
//                            ['name'=> 'analytic.entry_log.views','type'=> 1,'route_name'=> 'entry_log.views','icons_name'=> 'zmdi-user','display_name'=> 'View','descriptions'=> 'Ability to add new user records at "Users" page',],
//                            ['name'=> 'analytic.entry_log.update','type'=> 1,'route_name'=> 'entry_log.update','icons_name'=> 'zmdi-user','display_name'=> 'Update','descriptions'=> 'Ability to add new user records at "Users" page',],
//                            ['name'=> 'analytic.entry_log.export','type'=> 1,'route_name'=> 'entry_log.export','icons_name'=> 'zmdi-edit','display_name'=> 'Export','descriptions'=> 'Ability to edit user records at "Users" page'],
//                            ['name'=> 'analytic.entry_log.view_mapping','type'=> 1,'route_name'=> 'entry_log.mapping','icons_name'=> 'zmdi-','display_name'=> 'Mapping','descriptions'=> 'Ability to view mapping flag records "Entry Log" page'],
//                        ]
//                    ]
//                ]
//            ],

            // System
            [
                'name'          => 'system',
                'type'          => 2,
                'route_name'    => 'javascript:void(0);',
                'icons_name'    => 'zmdi-settings',
                'display_name'  => 'System',
                'descriptions'  => 'Ability to manage all configuration of the site',
                'child' => [
                    // Users
                    [
                        'name'          => 'system.user',
                        'type'          => 2,
                        'route_name'    => 'user.index',
                        'icons_name'    => 'zmdi-accounts',
                        'display_name'  => 'Users',
                        'descriptions' => '',
                        'child' => [
                            ['name'=> 'system.user.create','type'=> 1,'route_name'=> 'user.create','icons_name'=> 'zmdi-user','display_name'=> 'Create New User','descriptions'=> 'Ability to add new user records at "Users" page',],
                            ['name'=> 'system.user.edit','type'=> 1,'route_name'=> 'user.edit','icons_name'=> 'zmdi-edit','display_name'=> 'Edit User','descriptions'=> 'Ability to edit user records at "Users" page'],
                            ['name'=> 'system.user.destroy','type'=> 1,'route_name'=> 'user.destroy','icons_name'=> 'zmdi-delete','display_name'=> 'Delete User','descriptions'=> 'Ability to delete user records at "Users" page'],
                            ['name'=> 'system.user.role','type'=> 1,'route_name'=> 'user.role','icons_name'=> 'zmdi-', 'display_name'=> 'Assigning Roles','descriptions'=> 'Ability to delete user records at "Users" page'],
                            ['name'=> 'system.user.permission','type'=> 1,'route_name'=> 'user.permission','icons_name'=> 'zmdi-','display_name'=> 'Assigning Permissions','descriptions'=> 'Ability to delete user records at "Users" page'],
                        ]
                    ],
                    // Roles
                    [
                        'name'          => 'system.roles',
                        'type'          => 2,
                        'route_name'    => 'role.index',
                        'icons_name'    => 'zmdi-assignment-account',
                        'display_name'  => 'Roles',
                        'descriptions'  => 'Ability to export relevant data reports',
                        'child' => [
                            ['name'=> 'system.role.create','type'=> 1,'route_name'=> 'role.create','icons_name'=> 'zmdi-user','display_name'=> 'Create','descriptions'=> 'Ability to add new user records at "Users" page',],
                            ['name'=> 'system.role.edit','type'=> 1,'route_name'=> 'role.edit','icons_name'=> 'zmdi-edit','display_name'=> 'Edit','descriptions'=> 'Ability to edit user records at "Users" page'],
                            ['name'=> 'system.role.destroy','type'=> 1,'route_name'=> 'role.destroy','icons_name'=> 'zmdi-delete','display_name'=> 'Delete','descriptions'=> 'Ability to delete user records at "Users" page'],
                            ['name'=> 'system.role.permission','type'=> 1,'route_name'=> 'role.permission','icons_name'=> 'zmdi-','display_name'=> 'Assigning Permissions','descriptions'=> 'Ability to delete user records at "Users" page'],
                        ]
                    ],
                    // Permission
                    [
                        'name'          => 'system.permissions',
                        'type'          => 2,
                        'route_name'    => 'permissions',
                        'icons_name'    => 'zmdi-shield-check',
                        'display_name'  => 'Permissions',
                        'descriptions'  => 'Ability to export relevant data reports',
                        'child' => [
                            ['name'=> 'system.permission.create','type'=> 1,'route_name'=> 'permission.create','icons_name'=> 'zmdi-user','display_name'=> 'Create','descriptions'=> 'Ability to add new user records at "Users" page',],
                            ['name'=> 'system.permission.edit','type'=> 1,'route_name'=> 'permission.edit','icons_name'=> 'zmdi-edit','display_name'=> 'Edit','descriptions'=> 'Ability to edit user records at "Users" page'],
                            ['name'=> 'system.permission.destroy','type'=> 1,'route_name'=> 'permission.destroy','icons_name'=> 'zmdi-delete','display_name'=> 'Delete','descriptions'=> 'Ability to delete user records at "Users" page'],
                        ]
                    ],
                    // Cofing Site
//                    [
//                        'name'=>'system.config.site',
//                        'type'=> 2,
//                        'route_name'=> 'config.site',
//                        'icons_name'=> 'zmdi-settings-square',
//                        'display_name' => 'Config Site',
//                        'descriptions' => 'Ability to manage all configuration of the site',
//                        'child'=>[
//                            ['name'=> 'system.config.site.update','type'=> 1,'route_name'=> 'config.site.update','icons_name'=> 'zmdi-edit','display_name'=> 'Update','descriptions'=> 'Ability to edit user records at "Users" page'],
//                        ]
//                    ],

                    // Session Logs
                    [
                        'name'=> 'system.session_logs',
                        'type'=> 2,
                        'route_name'=> 'session_logs',
                        'icons_name'=> 'zmdi-arrow-right',
                        'display_name' => 'Session Logs',
                        'descriptions' => '',
                        'child'=>[]
                    ],
//
//                    // Operator Logs
//                    [
//                        'name'=> 'system.operator_log',
//                        'type'=> 2,
//                        'route_name'=> 'operator_log',
//                        'icons_name'=> 'zmdi-arrow-right',
//                        'display_name' => 'Operator Logs',
//                        'descriptions' => 'Show Operator Logs',
//                        'child'=>[]
//                    ],


                ]
            ],
        ];
    }
}
