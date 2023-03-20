<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RoleHasPermissions;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Array of associative array with 2 index, 'name' and 'permissions'
     * - name (string) is role name
     * - homepage_path (string) is default homepage after login
     * - permissions (array) is default permission, this list refered 
     *   to storage/app/modules/list.json > categories[n].groups[n].modules[n].name
     *   with 'access_' as prefix
     */
    protected $roles = [
        [
            'name' => 'Super Administrator',
            'permissions' => [
                "access_studyprogram_data",
                "access_manage_curriculum",
                "access_manage_subjects",
                "access_learning_method_type"
            ],
            'homepage_path' => '/curriculum'
        ]
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RoleHasPermissions::query()->delete();
        foreach($this->roles as $role){
            $_role = Role::firstOrCreate([
                'name' => $role['name'],
                'homepage_path' => $role['homepage_path']
            ]);
            $_role->givePermissions($role['permissions']);
        }
    }
}
