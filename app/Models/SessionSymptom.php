<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionSymptom extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'symptom_id',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ScreeningSession::class, 'session_id');
    }

    public function symptom(): BelongsTo
    {
        return $this->belongsTo(Symptom::class, 'symptom_id');
    }
}
