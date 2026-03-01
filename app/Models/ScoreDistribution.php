<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScoreDistribution extends Model
{
    use HasFactory;

    protected $table = 'score_distributions';

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'activity_id',
        'name',
        'weight',
    ];

    protected $casts = [
        'weight' => 'integer',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function studentScores()
    {
        return $this->hasMany(StudentScore::class, 'score_distribution_id');
    }
}