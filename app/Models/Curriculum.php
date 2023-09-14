<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Models\HasResource;

class Curriculum extends Model
{
    use HasResource;

    protected $table = "curriculums";

    protected $fillable = [
        'name', 'applied_date', 'studyprogram_id'
    ];

    protected $resourceColumn = [
        'book_document', 'sk_document', 'report_document', 'ba_document'
    ];

    protected $resourceFolder = [
        'book_document' => 'curriculum/documents',
        'sk_document' => 'curriculum/documents',
        'report_document' => 'curriculum/documents',
        'ba_document' => 'curriculum/documents'
    ];


    public function studyprogram()
    {
        return $this->belongsTo(Studyprogram::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
