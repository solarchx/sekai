<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentScore extends Model
{
    use HasFactory;

    protected $table = 'student_scores';

    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'activity_id',
        'student_id',
        'score_distribution_id',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function scoreDistribution()
    {
        return $this->belongsTo(ScoreDistribution::class, 'score_distribution_id');
    }
}