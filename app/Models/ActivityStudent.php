<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityStudent extends Pivot
{
    use SoftDeletes;

    protected $table = 'activity_students';

    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'student_id',
        'activity_id',
        'student_order',
    ];
}