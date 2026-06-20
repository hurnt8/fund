<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            'view loans',
            'manage loans',
            'view users',
            'manage users',
            'manage roles',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Roles
        $clientRole = Role::firstOrCreate(['name' => 'client']);
        $clientRole->syncPermissions(['view loans']);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(['view loans', 'manage loans', 'view users']);

        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->syncPermissions($permissions);

        // Default super-admin account
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@credixa.eu'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('Credixa@2025!'),
                'type'     => 'staff',
            ]
        );
        $superAdmin->syncRoles(['super-admin']);

        // Default admin account
        $admin = User::firstOrCreate(
            ['email' => 'admin@credixa.eu'],
            [
                'name'     => 'Admin Credixa',
                'password' => Hash::make('Admin@2025!'),
                'type'     => 'staff',
            ]
        );
        $admin->syncRoles(['admin']);

        $this->command->info('Roles, permissions and default accounts created.');
        $this->command->table(
            ['Role', 'Email', 'Password (change immediately)'],
            [
                ['super-admin', 'superadmin@credixa.eu', 'Credixa@2025!'],
                ['admin',       'admin@credixa.eu',      'Admin@2025!'],
            ]
        );
    }
}
