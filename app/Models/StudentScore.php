<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentScore extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'student_scores';

    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'activity_id',
        'student_id',
        'name',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}