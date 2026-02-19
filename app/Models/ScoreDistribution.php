<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScoreDistribution extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'score_distributions';

    public $incrementing = false;
    protected $primaryKey = null;

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
}