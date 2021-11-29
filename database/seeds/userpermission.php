<?php

use App\Permission;
use App\Role;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class userpermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //truncate table
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Illuminate\Support\Facades\DB::table('model_has_permissions')->truncate();
        \Illuminate\Support\Facades\DB::table('model_has_roles')->truncate();
        \Illuminate\Support\Facades\DB::table('role_has_permissions')->truncate();
//        \Illuminate\Support\Facades\DB::table('users')->truncate();
        \Illuminate\Support\Facades\DB::table('roles')->truncate();
        \Illuminate\Support\Facades\DB::table('permissions')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $user = User::where(['email' => 'hadidin4423@gmail.com'])->first();
        if ($user) {
            $this->command->warn('Superadmin already exist ' . $user->email);
            $confirm = $this->command->confirm('Do you want to update', false);

            if ($confirm) {
                $email = $this->command->ask("Enter email", 'hadidin4423@gmail.com');
                $password = $this->command->secret("Enter password [enter set as default]");
                $password = $password ? $password : '123456';

                $user->update([
                    'email' => $email,
                    'password' => Hash::make($password), // secret
                ]);
            }
        } else {

            $user = User::create([
                'name' => 'Superadmin',
                'email' => 'hadidin4423@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123456'), // secret
                'remember_token' => Str::random(10)
            ]);
        }

        $this->command->line('Here is your Superadmin details to login:');
        $this->command->warn($user->email);


        $role = \App\Role::create([
            'name' => 'Root',
            'display_name' => 'Super Administrator',
            'descriptions' => 'Super administrator has all permissions',
        ]);

        // Seed the default permissions
        $permissions = Permission::defaultPermissions();

        foreach ($permissions as $permission) {
            $perm1=Permission::create([
                'name'          => $permission['name'],
                'type'          => $permission['type'],
                'route_name'    => $permission['route_name'],
                'icons_name'    => $permission['icons_name'],
                'display_name'  => $permission['display_name'],
                'descriptions'  => $permission['descriptions']?$permission['descriptions']:'',
            ]);
            $role->givePermissionTo($perm1);
            $user->givePermissionTo($perm1);
            if (isset($permission['child'])) {
                foreach ($permission['child'] as $item) {
                    $perm2=Permission::create([
                        'parent_id'     => $perm1->id,
                        'name'          => $item['name'],
                        'type'          => $item['type'],
                        'route_name'    => $item['route_name'],
                        'icons_name'    => $item['icons_name'],
                        'display_name'  => $item['display_name'],
                        'descriptions'  => $item['descriptions']?$item['descriptions']:'',
                    ]);

                    $role->givePermissionTo($perm2);

                    $user->givePermissionTo($perm2);
                    if(isset($item['child'])){
                        foreach ($item['child'] as $last) {
                            $perm3=Permission::create([
                                'parent_id'     => $perm2->id,
                                'name'          => $last['name'],
                                'type'          => $last['type'],
                                'route_name'    => $last['route_name'],
                                'icons_name'    => $last['icons_name'],
                                'display_name'  => $last['display_name'],
                                'descriptions'  => $last['descriptions']?$last['descriptions']:'',
                            ]);
                            $role->givePermissionTo($perm3);
                            $user->givePermissionTo($perm3);
                        }
                    }
                }
            }
        }
        $user->assignRole($role);

        $this->command->info('Default Permissions added.');

        //Confirm roles needed
        // if ($this->command->confirm('Create Roles for user, default is admin and user?', true)) {

        // Ask for roles from input
        // $input_roles = $this->command->ask('Enter roles in comma separate format.', 'Admin,User');

        // Explode roles
        $roles_array = explode(',', 'Admin');
        $display = [
            'Admin' => 'Administrator',
            'User'  => 'Normal User',
        ];

        // add roles
        foreach($roles_array as $role) {
            $role = Role::firstOrCreate([
                'parent_id' => 1,
                'name' => trim($role),
                'display_name'=>trim($display[$role]),
                'descriptions'=>'',
                'guard_name' => 'web'
            ]);

            if( $role->name == 'Admin' ) {
                // assign all permissions
                $role->syncPermissions(Permission::where(['discarded'=>0])->get());
                $this->command->info('Admin granted all the permissions');
            } else {
                // for others by default only read access
                $this->oldUsersAssignRoles($role);
                $role->syncPermissions(Permission::where(['parent_id'=>0,'discarded'=>0])->get());
            }

            // create one user for each role
            $this->createUser($role);
        }

        // $this->command->info('Roles ' . $input_roles . ' added successfully');

        // } else {
        //     $role=Role::firstOrCreate(['name' => 'User','display_name'=>'Normal User','descriptions'=>'', 'guard_name' => 'web']);
        //     $this->oldUsersAssignRoles($role);
        //     $this->command->info('Added only default user role.');
        // }

        return "";

    }

    /**
     * Create a user with given role
     *
     * @param $role
     */
    private function createUser($role)
    {
        if ($role->name == 'Admin' ) {
            $user = User::where(['email' => 'admin@ultd.com'])->first();

            if ($user) {
                $this->command->warn('Admin already exist ' . $user->email);
                $confirm = $this->command->confirm('Do you want to update', false);

                if ($confirm) {
                    $email = $this->command->ask("Enter email", 'admin@ultd.com');
                    $password = $this->command->secret("Enter password [enter set as default]");
                    $password = $password ? $password : '123456';

                    $user->update([
                        'email' => $email,
                        'password' => Hash::make($password), // secret
                    ]);
                }
            } else {

                $user = User::create([
                    'name' => 'Admin',
                    'email' => 'admin@ultd.com',
                    'email_verified_at' => now(),
                    'password' => Hash::make('123456'), // secret
                    'remember_token' => Str::random(10)
                ]);
            }

            $this->command->line('Here is your admin details to login:');
            $this->command->warn($user->email);
        } else {
            $user = factory(User::class)->create();
        }

        $user->assignRole($role->name);
    }


    private function oldUsersAssignRoles($role){
        $this->command->info('Group old users into regular user roles.');
        $users = User::where('status',1)->get();
        foreach ($users as $user) {
            if(!$user->hasAnyRole(Role::all())){
                if($user->assignRole($role->name)){
                    $this->command->info("The user `{$user->name}` was successfully classified as a normal user group.");
                }else{
                    $this->command->info("The user `{$user->name}` was classified as a normal user group failure.");
                }
            }
        }
    }
}
