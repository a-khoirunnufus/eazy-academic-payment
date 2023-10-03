<?php

namespace App\Traits;

use App\Helpers\AssociateData;
use App\Models\UserAssociateModel;

trait HasAssociateData
{
    protected $loadedData = [];
    protected $loadedProperty = [];
    protected $hasAssocDataCache = [];

    public function setAssociateData($name, $value = null)
    {
        $associateLists = array_keys(AssociateData::getAssociateList());
        if(!in_array($name, $associateLists))
            return false;

        $this->associateModels()->updateOrCreate(
            ['model' => $name],
            ['associate_identifier' => $value]
        );

        return true;
    }

    public function removeAssociateData($name)
    {
        $this->associateModels()
            ->where("model", $name)
            ->delete();
    }

    public function getAssociateData($name)
    {
        if(!isset($this->loadedData[$name])){
            $this->loadedData[$name] = $this->associateModels()
                ->where('model', $name)
                ->first() ?? -1;
        }

        if(!is_object($this->loadedData[$name]))
            return false;

        $modelAlias = $this->loadedData[$name]->model;
        $identifier = $this->loadedData[$name]->associate_identifier;
        if($identifier == null)
            return null;

        return AssociateData::getAssociatedData($modelAlias, $identifier);
    }

    public function getAssociateDataIdentifier($name)
    {
        if(!isset($this->loadedData[$name])){
            $model = $this->associateModels()->where('model', $name)->first();
            $this->loadedData[$name] = $model;
        } else {
            $model = $this->loadedData[$name];
        }

        if(!$model)
            return null;

        return $model->associate_identifier;
    }

    public function getAssociateDataProperty($name, $propName)
    {
        $cacheExists = true;
        if(!isset($this->loadedProperty[$name]))
            $cacheExists = false;
        else if (!isset($this->loadedProperty[$name]))
            $cacheExists = false;

        if(!$cacheExists)
            $this->loadedProperty[$name][$propName] = $this->getAssociateData($name)?->{$propName};

        return $this->loadedProperty[$name][$propName];
    }

    public function __assoc($name){
        return $this->getAssociateData($name);
    }

    public function hasAssociateData($name)
    {
        if(!isset($this->hasAssocDataCache[$name]))
            return $this->hasAssocDataCache[$name] = $this->getAssociateData($name) !== false;

        return $this->hasAssocDataCache[$name];
    }

    public static function findThroughAssociateData($model, $value)
    {
        return UserAssociateModel::where('model', $model)
                                 ->where('associate_identifier', $value)
                                 ->first()?->user;
    }
}
