<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Symptom extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'category',
        'description',
    ];

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(Disease::class, 'symptom_disease_map', 'symptom_id', 'disease_id')
            ->withPivot('weight')
            ->withTimestamps();
    }
}
