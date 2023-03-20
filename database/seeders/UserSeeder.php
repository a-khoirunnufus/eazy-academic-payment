<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => 'admin123'
            ]
        ];

        foreach($users as $user){
            $password = $user['password'];
            $_user = User::updateOrCreate([
                'email' => $user['email']
            ], [
                'name' => $user['name'],
                'password' => '$EAZYBANGET$' . substr(md5('$EAZYBANGET$'. $password).md5($password), 0, 50)
            ]);

            if($_user->roles()->count() == 0)
                $_user->roles()->create([
                    'role_id' => Role::first()?->id
                ]);
        }
    }
}
