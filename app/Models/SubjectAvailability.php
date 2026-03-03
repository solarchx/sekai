<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectAvailability extends Model
{
    use HasFactory;

    protected $table = 'subject_availabilities';

    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'major_id',
        'subject_id',
        'grade_id',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }
}