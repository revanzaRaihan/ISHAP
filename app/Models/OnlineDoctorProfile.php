<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineDoctorProfile extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'name',
        'platform',
        'profile_url',
        'specialty',
        'hospital',
    ];
}
