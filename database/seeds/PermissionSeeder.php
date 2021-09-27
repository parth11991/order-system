<?php

use App\Role;
use App\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = Permission::defaultPermissions();

        // create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roleSuperAdmin = Role::create(['name' => 'superadmin']);
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleSupplier = Role::create(['name' => 'supplier']);

        $roleSuperAdmin->syncPermissions(Permission::all());
        $roleAdmin->syncPermissions(Permission::all());
        $roleSupplier->syncPermissions(Permission::all());

    }
}
