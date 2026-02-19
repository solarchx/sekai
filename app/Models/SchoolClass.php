<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'major_id',
        'grade_id',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function students()
    {
        return $this->hasMany(User::class, 'class_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'class_id');
    }
}