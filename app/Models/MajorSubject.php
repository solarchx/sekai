<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class MajorSubject extends Pivot
{
    use SoftDeletes;

    protected $table = 'majors_subjects';

    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'major_id',
        'subject_id',
    ];
}