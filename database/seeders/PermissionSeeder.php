<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Storage;
use App\Models\Permission;
use DB;
use App\Models\RoleHasPermissions;

class PermissionSeeder extends Seeder
{
    // default_module_permission_prefix
    protected $default_prefix = "access";
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $now = date('Y-m-d H:i:s');
            // Seed default (access) permission for module
            $this->seedDefaultModulePermissions();
            // Seed additional permission for module
            $this->seedAdditionalModulePermissions();
            // remove un noticed data
            Permission::where('updated_at' , '<', $now)
                      ->delete();

            RoleHasPermissions::whereNotIn('permission_id', Permission::select('id'))->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function seedDefaultModulePermissions(): void
    {
        $modules = $this->getModules();
        foreach($modules as $module){
            Permission::updateOrCreate([
                'module_name' => $module['name'],
                'default_module_permission' => true,
                'name' => $this->default_prefix."_".$module['name']
            ], [
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    private function seedAdditionalModulePermissions(): void
    {
        $data = json_decode(Storage::get('modules/permissions.json'), true);
        foreach($data as $module){
            foreach($module['permissions'] as $permission){
                $permission_name = $permission['name'];
                if($permission_name == "__DEFAULT__")
                    $permission_name = $this->default_prefix."_".$module['module_name'];

                $_permission = Permission::updateOrCreate([
                    'module_name' => $module['module_name'],
                    'name' => $permission_name
                ], [
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                if(isset($permission['associate_models'])){
                    $_permission->associateModels()->delete();
                    $_permission->associateModels()->createMany(array_map(function($item){
                        return [
                            'model' => $item
                        ];
                    }, $permission['associate_models']));
                }
            }
        }
    }

    private function getModules(): array
    {
        $modules = [];
        $data = json_decode(Storage::get('modules/list.json'), true);
        foreach($data['categories'] as $category){
            foreach($category['groups'] as $group){
                foreach($group['modules'] as $module){
                    $modules[] = $module;
                }
            }
        }

        return $modules;
    }
}
