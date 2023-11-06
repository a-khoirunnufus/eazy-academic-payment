<?php

namespace App\Http\Controllers\_Payment\Api\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payment\Studyprogram;

class StudyprogramController extends Controller
{
    function getProdi($faculty)
    {
        $data = Studyprogram::where('faculty_id', '=', $faculty)->get();

        return $data;
    }
}
