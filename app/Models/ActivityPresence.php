<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityPresence extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activity_presences';

    protected $fillable = [
        'form_id',
        'student_id',
        'score',
        'location',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function form()
    {
        return $this->belongsTo(ActivityForm::class, 'form_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function report()
    {
        return $this->hasOne(ActivityReport::class, 'presence_id');
    }
}