<?php

namespace App\Observers;

use App\Models\Curriculum;

class CurriculumObserver
{
    public function updated(Curriculum $curriculum)
    {
        if($curriculum->studyprogram_id != $curriculum->getOriginal('studyprogram_id')){
            $curriculum->subjects()->update([
                'studyprogram_id' => $curriculum->studyprogram_id
            ]);
        }
    }
}
