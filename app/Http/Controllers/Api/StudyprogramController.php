<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Studyprogram;

class StudyprogramController extends Controller
{
    public function index()
    {
        $query = Studyprogram::query();

        $datatable = datatables($query);

        return $datatable->toJSON();
    }
}
