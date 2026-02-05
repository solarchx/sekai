<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    public $timestamps = false; // No timestamps

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'grade_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'grades_subjects');
    }
}
