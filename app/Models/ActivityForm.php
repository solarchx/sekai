<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activity_forms';

    protected $fillable = [
        'activity_id',
        'activity_date',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function presences()
    {
        return $this->hasMany(ActivityPresence::class, 'form_id');
    }
}