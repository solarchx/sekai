<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activity_reports';

    public $incrementing = false;
    protected $primaryKey = 'presence_id';
    protected $keyType = 'int';

    protected $fillable = [
        'presence_id',
        'score',
        'topic',
        'details',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function presence()
    {
        return $this->belongsTo(ActivityPresence::class, 'presence_id');
    }
}