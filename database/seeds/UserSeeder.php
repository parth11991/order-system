<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = new User();
        $user->name = 'Super Admin';
        $user->email = 'superadmin@gmail.com';
        $user->position = 'Owner';
        $user->biography = '<p>Super Admin&nbsp;Biography</p>';
        $user->dateOfBirth = '2003-04-30';
        $user->password = bcrypt('password'); // password
        $user->save();
        $user->assignRole('superadmin');

        $user = new User();
        $user->name = 'Admin';
        $user->email = 'admin@gmail.com';
        $user->position = 'Owner';
        $user->biography = '<p>Admin&nbsp;Biography</p>';
        $user->dateOfBirth = '2003-04-30';
        $user->password = bcrypt('password'); // password
        $user->save();
        $user->assignRole('admin');

        $user = new User();
        $user->name = 'Supplier Member';
        $user->email = 'supplier@gmail.com';
        $user->position = 'Supplier';
        $user->biography = '<p>Supplier&nbsp;Biography</p>';
        $user->dateOfBirth = '2003-04-30';
        $user->password = bcrypt('password'); // password
        $user->save();
        $user->assignRole('supplier');

    }
}
