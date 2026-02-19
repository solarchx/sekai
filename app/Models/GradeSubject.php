<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeSubject extends Pivot
{
    use SoftDeletes;

    protected $table = 'grades_subjects';

    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'grade_id',
        'subject_id',
    ];
}