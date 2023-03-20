<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;
use Storage;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = json_decode(Storage::get('modules/list.json'), true);

        $now = date('Y-m-d H:i:s');

        foreach($list['categories'] as $category){
            foreach($category['groups'] as $group){
                foreach($group['modules'] as $module){
                    Module::updateOrCreate(
                        $module,
                        ['updated_at' => $now]
                    );
                }
            }
        }

        Module::where('updated_at' , '<', $now)
                ->delete();
    }
}
