<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Saison extends Model
{
    protected $table = 'saisons';

    public $timestamps = false;

    protected $fillable = ['annee_debut', 'annee_fin', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function adhesions(): HasMany
    {
        return $this->hasMany(Adhesion::class);
    }

    public function typesAdhesion(): HasMany
    {
        return $this->hasMany(TypeAdhesion::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function getNomAttribute(): string
    {
        return $this->annee_debut.'-'.$this->annee_fin;
    }
}
