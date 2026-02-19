<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'identifier',
        'password',
        'role',
        'class_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => 'string',
    ];

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function taughtActivities()
    {
        return $this->hasMany(Activity::class, 'teacher_id');
    }

    public function activitiesAsStudent()
    {
        return $this->belongsToMany(Activity::class, 'activity_students', 'student_id', 'activity_id')
                    ->withTimestamps()
                    ->withPivot('deleted_at');
    }

    public function sentAnnouncements()
    {
        return $this->hasMany(Announcement::class, 'sender_id');
    }

    public function activityPresences()
    {
        return $this->hasMany(ActivityPresence::class, 'student_id');
    }

    public function studentScores()
    {
        return $this->hasMany(StudentScore::class, 'student_id');
    }
}