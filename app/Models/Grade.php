<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'grades';

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
    ];

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'grade_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'grades_subjects')
                    ->withTimestamps()
                    ->withPivot('deleted_at');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'grade_id');
    }
}