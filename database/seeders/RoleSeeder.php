<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roles to create (add more as needed)
        $roles = [
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'user',  'guard_name' => 'web'],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(
                ['name' => $r['name'], 'guard_name' => $r['guard_name']]
            );
        }

        // OPTIONAL: automatically assign 'admin' to user id 1 if you want
        // (uncomment if you want this behaviour)
        //
        // if ($user = \App\Models\User::find(1)) {
        //     $user->assignRole('admin');
        // }
    }
}
