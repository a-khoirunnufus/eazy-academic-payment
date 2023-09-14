<?php

namespace App\Traits\Authentication;

use Storage;
use App\Models\Module;

trait CustomGuard
{
    public function can($permissionName)
    {
        return in_array($permissionName, $this->getPermissions());
    }

    public function canAccessPath($path)
    {
        $allowedRoutes = $this->session->get('allowedRoutes');
        if(in_array($path, $allowedRoutes))
            return true;

        $modules = Module::where('path', $path)->get();

        $able = false;
        foreach ($modules as $module) {
            $access = "access_".$module->name;
            $able = $able || $this->can($access);
        }

        if($modules->count() == 0)
            $able = true;

        if($able){
            $allowedRoutes[] = $path;
            $this->session->put('allowedRoutes', $allowedRoutes);
        }

        return $able;
    }

    public function mountUserRoles($user)
    {
        $user->load('roles.role');
        // set available roles
        $this->setAvailableRoles($user->roles->map(function($item){
            return $item->role;
        }));
        // set active role
        $default = $user->roles->filter(function($item){
            return $item->is_default_role == true;
        })->first();

        if($default){
            $activeRole = $default->role;
        } else {
            $activeRole = $user->roles->first()->role;
        }

        $this->setActiveRole($activeRole);
    }

    public function getAvailableRoles()
    {
        return $this->session->get("availableRoles");
    }

    public function getActiveRole()
    {
        return $this->session->get("activeRole");
    }

    public function getPermissions()
    {
        return $this->session->get("permissions");
    }

    public function setAvailableRoles($roles)
    {
        $this->session->put("availableRoles", $roles);
    }

    public function setActiveRole($role)
    {
        $this->session->put("activeRole", $role);

        $this->session->put("allowedRoutes", []);

        $role->load('permissions.permission');

        $this->setPermissions($role->permissions->map(function($item){
            return $item->permission->name;
        })->toArray());
    }

    public function setPermissions($permissions)
    {
        $this->session->put("permissions", $permissions);
        $this->setAvailableModules($permissions);
    }

    public function getAvailableModules()
    {
        return $this->session->get('availableModules');
    }

    public function setAvailableModules($permissions)
    {
        $list = json_decode(Storage::get('modules/list.json'), true);

        $result = [];

        foreach($list['categories'] as $category){
            $groups = [];
            foreach($category['groups'] as $group){
                $modules = [];
                foreach($group['modules'] as $module){
                    if(in_array('access_'.$module['name'], $permissions))
                        $modules[] = $module;

                }

                if(count($modules) == 0)
                    continue;

                $groups[] = [
                    'name' => $group['name'],
                    'icon' => $group['icon'],
                    'modules' => $modules
                ];
            }

            if(count($groups) == 0)
                continue;

            $result[] = [
                'name' => $category['name'],
                'groups' => $groups
            ];
        }

        $this->session->put('availableModules', $result);
    }
}
