<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Resource;
use Carbon\Carbon;

trait HasResource
{
    protected static function bootHasResource()
    {
        static::deleting(function (Model $model) {
            self::removeFiles($model);
        });

        static::updating(function (Model $model) {
            self::removeFiles($model, true);
        });
    }

    protected static function removeFiles($model, $onUpdate = false)
    {
        $resourcesColumns = $model->resourceColumn;
        foreach ($resourcesColumns as $column) {
            if($model->{$column} == $model->getOriginal($column) && $onUpdate)
                continue;
            if($model->getOriginal($column)){
                try {
                    Storage::cloud()->delete($model->getOriginal($column));
                } catch (\Exception $e) {}
            }
        }
    }

    public static function getResourceColumns()
    {
        $model = get_called_class();
        return (new $model)->_getResourceColumns();
    }

    public function _getResourceColumns()
    {
        return $this->resourceColumn;
    }

    /** 
     * @param string $columnName
     * @param string $tmpResourceId id from \App\Models\Temporary\Resource
     * @param string $fileName new File name (extention will follow original resource on temporary)
     */
    public function setResourceFromTemporary($columnName, $tmpResourceId, $fileName = null)
    {
        $resource = Resource::find($tmpResourceId);
        
        $filepathArr = explode("/", $resource->filepath);
        $tmpFileName = $filepathArr[count($filepathArr) - 1];
        $newFileName = $tmpFileName;

        if(!is_null($fileName)){
            $tmpFileNameArr = explode(".", $tmpFileName);
            $primaryKey = $this->primaryKey ?? "id";
            $newFileName = $this->{$primaryKey} . "/" . $fileName.".".$tmpFileNameArr[count($tmpFileNameArr) - 1];
        }

        $newFilePath = $this->resourceFolder[$columnName] . "/" . $newFileName;
        
        Storage::cloud()->move($resource->filepath, $newFilePath);
        
        $this->{$columnName} = $newFilePath;
        $this->save();

        $resource->delete();
    }

    public function getResourcesAttribute()
    {
        $result = [];
        foreach ($this->resourceColumn as $item) {
            $result[$item] = null;
            
            if(is_null($this->{$item}))
                continue;

            try {
                $result[$item] = Storage::disk('minio_read')
                                        ->temporaryUrl(
                                            $this->{$item}, 
                                            Carbon::now()->addMinutes(60)
                                        );
            } catch (\Exception $e) {}
        }

        return $result;
    }
}
