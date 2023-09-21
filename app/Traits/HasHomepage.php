<?php

namespace App\Traits;

use App\Providers\RouteServiceProvider;

trait HasHomepage
{
    public function getHompagePath()
    {
        if(auth()->user()->hasAssociateData("student"))
            return RouteServiceProvider::STUDENT_HOMEPAGE;
        else
            return RouteServiceProvider::ADMIN_HOMEPAGE;
    }
}