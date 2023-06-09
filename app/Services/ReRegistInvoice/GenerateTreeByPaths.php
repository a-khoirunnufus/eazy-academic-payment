<?php

namespace App\Services\ReRegistInvoice;

class GenerateTreeByPaths {

    private $paths;
    private $id = 0;

    public function __construct($paths)
    {
        $this->paths = $paths;
    }

    public function generate()
    {
        $tree = [];

        foreach ($this->paths as $path) {
            $level = &$tree;

            $parts = explode("/", $path);

            foreach($parts as $part) {
                if (!$this->findByName($level, $part)) {
                    $level[] = [
                        'id' => $this->genId(),
                        'data' => [
                            'obj_type' => explode('_', $part)[0],
                            'obj_id' => explode('_', $part)[1],
                        ],
                        "children" => [],
                        "name" => $part,
                    ];
                }

                $level = &$level[count($level)-1]["children"];
            }
        }

        return $tree;
    }

    private function findByName(&$array, $name) {
        foreach($array as &$item) {
            if (strcmp($item["name"], $name) === 0) {
                return $item;
            }
        }
        return false;
    }

    // public function generate()
    // {
    //     $tree = [];

    //     foreach($this->paths as $path){
    //         $splitPath = preg_split('/\//', $path);
    //         $this->addChild($tree, $splitPath);
    //     }

    //     return $tree;
    // }

    // private function addChild(&$arr, &$splitPath){
    //     $parent = array_shift($splitPath);
    //     //check for $parent in $tree array
    //     $foundParent = 0;
    //     foreach($arr as &$item){
    //         if($item['id'] == $parent){
    //             if(count($splitPath) > 0){$this->addChild($item['children'], $splitPath);}
    //             $foundParent = 1;
    //         }
    //     }
    //     //if not found, add to array
    //     if($foundParent == 0){
    //         $parent_arr = explode('_', $parent);
    //         $arr[] = array(
    //             'id' => $this->genId(),
    //             'data' => [
    //                 'obj_type' => $parent_arr[0],
    //                 'obj_id' => $parent_arr[1],
    //             ],
    //             'children' => []
    //         );
    //         if(count($splitPath) > 0){$this->addChild($arr[count($arr)-1]['children'], $splitPath);}
    //     }
    // }

    private function genId()
    {
        $this->id++;
        return $this->id;
    }
}
