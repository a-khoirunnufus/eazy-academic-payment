<?php

namespace App\Traits\Models;

use App\Models\Permission;

trait HasPermissions
{
    public function givePermissions($permissions)
    {
        $data = Permission::query();

        if(gettype($permissions) == 'array'){
            $data->whereIn('name', $permissions);
        } else {
            $data->where('name', $permissions);
        }

        $permissions = $data->get();
        if($permissions->count() == 0)
            return false;

        $this->permissions()
             ->createMany($permissions->map(function($item){
                return [
                    'permission_id' => $item->id
                ];
             })->toArray());

        return true;
    }

    public function revokePermission($permission_name)
    {
        $permission_id = Permission::where('name', $permission_name)
            ->first()
            ?->id;

        if(is_null($permission_id))
            return false;

        $this->permissions()->where('permission_id', $permission_id)->delete();

        return true;
    }

    public function hasPermission($permission_name)
    {
        $permission_id = Permission::where('name', $permission_name)
            ->first()
            ?->id;

        if(is_null($permission_id))
            return false;

        return $this->permissions()->where('permission_id', $permission_id)->exists();
    }
}