<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'deleted'];

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'major_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'majors_subjects');
    }
}
