<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScreeningSession extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'status',
    ];

    public function symptoms(): BelongsToMany
    {
        return $this->belongsToMany(Symptom::class, 'session_symptoms', 'session_id', 'symptom_id')
            ->withTimestamps();
    }

    public function sessionSymptoms(): HasMany
    {
        return $this->hasMany(SessionSymptom::class, 'session_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(ScreeningResult::class, 'session_id')->orderByDesc('confidence_score');
    }

    public function topResult(): HasOne
    {
        return $this->hasOne(ScreeningResult::class, 'session_id')->latestOfMany('confidence_score');
    }
}
