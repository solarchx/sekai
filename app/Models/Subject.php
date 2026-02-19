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

    public function majors()
    {
        return $this->belongsToMany(Major::class, 'majors_subjects')
                    ->withTimestamps()
                    ->withPivot('deleted_at');
    }

    public function grades()
    {
        return $this->belongsToMany(Grade::class, 'grades_subjects')
                    ->withTimestamps()
                    ->withPivot('deleted_at');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'subject_id');
    }
}