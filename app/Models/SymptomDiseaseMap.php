<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SymptomDiseaseMap extends Model
{
    use HasFactory;

    protected $table = 'symptom_disease_map';

    protected $fillable = [
        'symptom_id',
        'disease_id',
        'weight',
    ];

    protected $casts = [
        'weight' => 'float',
    ];

    public function symptom(): BelongsTo
    {
        return $this->belongsTo(Symptom::class, 'symptom_id');
    }

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class, 'disease_id');
    }
}
