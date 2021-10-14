<?php

namespace App\Http\Controllers;

use App\Model\OperatorLogModel;
use Session;
use App\Role;
use App\User;
use App\Permission;
use App\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    // use Authorizable;

    private $currentPath;
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
        // get current user role and children roles
//        $roles = Role::getAllChildrenRoles($request);

        // get current user and direct children roles
        $user = $request->user();
        $current_user_id = $user->id;
        $roleData = Role::getUserRoleData($current_user_id);
        $roles = Role::getDirectChildrenRoles($roleData->role_id);
        $roleIds = array_column($roles, 'id');

        // get userIds
        $userIds = User::getUserIds($roleIds);
        $result = User::whereIn('id',$userIds)->latest()->paginate(10);
//        dd($result);
        return View::make('user.index',compact('result','current_user_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // get current user role and children roles
//        $roles = Role::getAllChildrenRoles($request);

        // get current user and direct children roles
        $currentUser = $request->user();
        $roleData = Role::getUserRoleData($currentUser->id);
        $roles = Role::onlyGetDirectChildrenRoles($roleData->role_id);
        return View::make('user.create',compact('roles','currentUser'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'bail|required|min:2',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'roles' => 'required|min:1'
        ]);
        // add operator log
        OperatorLogModel::insertLog($request, 'Created','User','Attempted create user [email]=['.$request->email.']');

        // hash password
        $request->merge(['password' => bcrypt($request->get('password'))]);

        // Create the user
        if ( $user = User::create($request->except('roles', 'permissions')) ) {
            // add roles for user
            $roles = $request->get('roles',[]);
            $roles = Role::whereIn('id', $roles)->pluck('id')->toArray();
            $user->syncRoles($roles);
            Session::flash('alert-success', 'User has been created.');
        } else {
            Session::flash('alert-danger', 'Unable to create user.');
         }

        return Redirect::route('user.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        // get choose user
        $user = User::findOrFail($id);

        // get current user
        $currentUser = $request->user();
        $roleData = Role::getUserRoleData($currentUser->id);
//        $roles = Role::get();
//        foreach ($roles as $role){
//            $role->own = $user->hasRole($role) ? true : false;
//        }
        // get current user role and children roles
//        $roles = Role::getAllChildrenRoles($request);

        // get direct children roles
        $roles = [];
        if ($id != $currentUser->id) {
            $roles = Role::onlyGetDirectChildrenRoles($roleData->role_id);
            foreach ($roles as &$role){
                $role['own'] = $user->hasRole($role['id']) ? true : false;
            }
        }

        return View::make('user.edit',compact('user','roles','currentUser'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'bail|required|min:2',
            'email' => 'required|email|unique:users,email,' . $id,
        ];

        // dont need to edit current user's role
        $currentUser = $request->user();
        if ($id > 1 && $currentUser->id != $id) {
            $rules['roles'] = 'required|min:1';
        }
        $this->validate($request, $rules);

        // add operator log
        OperatorLogModel::insertLog($request, 'Updated','User','Attempted update user [email]=['.$request->email.']');

        // Get the user
        $user = User::findOrFail($id);

        // Update user
        $user->fill($request->except('roles', 'permissions', 'password'));

        // check for password change
        if($request->get('password')) {
            $user->password = bcrypt($request->get('password'));
        }

        // update role for user, except current user's role
        if ($id > 1 && $currentUser->id != $id) {
            $roles = $request->get('roles',[]);
            $roles = Role::whereIn('id', $roles)->pluck('id')->toArray();

            $user->syncRoles($roles);
        }

        $user->save();

        Session::flash('alert-success', "User `{$user->name}` has  been updated.");

        return redirect()->route('user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @internal param Request $request
     */
    public function destroy(Request $request)
    {
        // add operator log
        OperatorLogModel::insertLog($request, 'Deleted','User','Attempted delete user [id]=['.$request->id.']');
        if ( Auth::user()->id == $request->get('id') ) {
            return [
                'code' => 1,
                'message' => 'Deletion of currently logged in user is not allowed.',
            ];
        }

        if ( $request->get('id') == 1 ) {
            return [
                'code' => 1,
                'message' => 'The super admin user that comes with the system cannot be deleted.',
            ];
        }

        if( User::findOrFail($request->get('id'))->delete() ) {
            return [
                'code' => 0,
				'message' => 'User has been deleted.',
            ];
            // Session::flash('alert-success', 'User has been deleted.');
        } else {
            return [
                'code' => 1,
				'message' => 'User not deleted.',
            ];
//            Session::flash('alert-danger', 'User not deleted.');
        }
        // return redirect()->back();
    }

    /**
     * Assign direct permission page display
     *
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function permission($id)
    {
        $user = User::findOrFail($id);
        $permissions = Permission::with(['allChilds'=>function($query){
            $query->where(['discarded'=>0]);
        }])->where(['parent_id'=>0,'discarded'=>0])->get();
        foreach ($permissions as $p1){
            $p1->own = $user->hasDirectPermission($p1->id) ? 'checked' : '' ;
            if ($p1->allChilds->isNotEmpty()){
                foreach ($p1->allChilds as $p2){
                    $p2->own = $user->hasDirectPermission($p2->id) ? 'checked' : '' ;
                    if ($p2->allChilds->isNotEmpty()){
                        foreach ($p2->allChilds as $p3){
                            $p3->own = $user->hasDirectPermission($p3->id) ? 'checked' : '' ;
                        }
                    }
                }
            }
        }
        return View::make('user.permission',compact('user','permissions'));
    }


    public function assignPermission(Request $request,$id)
    {
        // add operator log
        OperatorLogModel::insertLog($request, 'AssignPermission','User','Attempted assign permission to user [id]=['.$id.']');
        $user = User::findOrFail($id);
        $permissions = $request->get('permissions',[]);
        try{
            $user->syncPermissions($permissions);
            Session::flash('alert-success', "User `{$user->name}`  permissions has been updated.");
            return Redirect::to(URL::route('user.index'));
        }catch (\Exception $exception){
            Session::flash('alert-danger', "This user `{$user->name}` unable to assign permissions.");
            return Redirect::back();
        }
    }


    public function changePassword(Request $request){
        if($request->isMethod('post')){
           $this->validate($request,
               [
                    'currentPassword'=>'required',
                    'password'=>'required|confirmed|min:6|max:30',
                    'password_confirmation'=>['required',"same:password"],
               ],
               [
                   'password.min' => 'The password must be at least 6 characters.',
                   'password.max' => 'The password can\'t greater than 30 characters ',
               ]
           );
            $result = User::changePassword($request->all());
            if($result['status']){
                Auth::logout();
                return redirect('/login')->with('successMessage','Password changed successfully');
            }else{
                return view('user.changePassword')->withErrors(['main'=>$result['message']]);
            }
        }
        return view('user.changePassword');
    }

}
