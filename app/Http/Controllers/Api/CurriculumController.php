<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curriculum;
use App\Traits\Models\QueryFilterByRequest;
use App\Traits\Models\LoadDataRelationByRequest;
use App\Enums\Curriculum\DocumentFileName;
use App\Http\Requests\Curriculum\StoreRequest;
use DB;

class CurriculumController extends Controller
{
    use QueryFilterByRequest, LoadDataRelationByRequest;

    public function index(Request $request)
    {
        $query = Curriculum::query();
        $query = $this->applyFilter($query, $request, ['studyprogram_id']);
        $query = $this->applyRelation($query, $request, ['studyprogram']);

        $datatable = datatables($query);
        // Prepare for document url
        foreach(Curriculum::getResourceColumns() as $column){
            $datatable->editColumn($column, function($row) use($column){
                return $row->resources[$column];
            });
        }
        $datatable->rawColumns(Curriculum::getResourceColumns());

        return $datatable->toJSON();
    }

    public function show(Curriculum $curriculum)
    {
        $curriculum->load('studyprogram');
        foreach($curriculum->resources as $key => $resource){
            $curriculum[$key] = $resource;
        }
        return response()->json($curriculum);
    }

    public function store(StoreRequest $request)
    {
        DB::beginTransaction();
        $validated = $request->validated();

        try {
            $curriculum = Curriculum::create($validated);
            $this->uploadDocuments($curriculum, $validated);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            return $e;
        }
        
        return response()->json(['success' => true]);
    }

    public function update(StoreRequest $request, Curriculum $curriculum)
    {
        DB::beginTransaction();
        $validated = $request->validated();

        try {
            $curriculum->update($validated);
            $this->uploadDocuments($curriculum, $validated);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            return $e;
        }
        
        return response()->json(['success' => true]);
    }

    public function destroy(Curriculum $curriculum)
    {
        $curriculum->delete();
        
        return response()->json(['success' => true]);
    }

    private function uploadDocuments($curriculum, $validated)
    {
        foreach(Curriculum::getResourceColumns() as $column){
            if(isset($validated[$column])){
                $curriculum->setResourceFromTemporary(
                    $column,
                    $validated[$column],
                    call_user_func(DocumentFileName::class.'::'.$column)
                );
            }
        }
    }
}
