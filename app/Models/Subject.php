<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subjects';

    protected $fillable = [
        'name',
    ];

    public function availabilities()
    {
        return $this->hasMany(SubjectAvailability::class, 'subject_id');
    }

    public function majors()
    {
        return $this->belongsToMany(Major::class, 'subject_availabilities', 'subject_id', 'major_id')
                    ->withPivot('grade_id')
                    ->withTimestamps();
    }

    public function grades()
    {
        return $this->belongsToMany(Grade::class, 'subject_availabilities', 'subject_id', 'grade_id')
                    ->withPivot('major_id')
                    ->withTimestamps();
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'subject_id');
    }
}