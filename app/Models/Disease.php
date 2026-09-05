<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disease extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'severity_level',
        'description',
        'pathogenesis_overview',
        'pathogenesis_causes',
        'pathogenesis_risk_factors',
        'recovery_tips',
        'red_flags',
    ];

    protected $casts = [
        'pathogenesis_causes' => 'array',
        'pathogenesis_risk_factors' => 'array',
        'recovery_tips' => 'array',
        'red_flags' => 'array',
    ];

    public function symptoms(): BelongsToMany
    {
        return $this->belongsToMany(Symptom::class, 'symptom_disease_map', 'disease_id', 'symptom_id')
            ->withPivot('weight')
            ->withTimestamps();
    }

    public function symptomMaps(): HasMany
    {
        return $this->hasMany(SymptomDiseaseMap::class, 'disease_id');
    }
}
