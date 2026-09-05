<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreeningResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'disease_id',
        'confidence_score',
        'matched_symptoms_count',
        'total_symptoms_for_disease',
        'reasoning',
    ];

    protected $casts = [
        'confidence_score' => 'float',
        'matched_symptoms_count' => 'integer',
        'total_symptoms_for_disease' => 'integer',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ScreeningSession::class, 'session_id');
    }

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class, 'disease_id');
    }
}
