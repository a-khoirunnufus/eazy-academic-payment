<?php

namespace App\Traits;

use App\Providers\RouteServiceProvider;

trait HasHomepage
{
    public function getHompagePath()
    {
        $defaultHomepage = auth()->getActiveRole()->homepage_path;
        if(!is_null($defaultHomepage))
            return $defaultHomepage;

        foreach(auth()->getAvailableModules() as $category){
            foreach($category['groups'] as $group){
                foreach($group['modules'] as $module){
                    if($module['path'] != "#")
                        return $module['path'];                    
                }
            }
        }

        return RouteServiceProvider::HOME;
    }
}