<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeAdhesion extends Model
{
    protected $table = 'types_adhesion';

    public $timestamps = false;

    protected $fillable = ['nom', 'saison_id'];

    public function adhesions(): HasMany
    {
        return $this->hasMany(Adhesion::class);
    }

    public function saison(): BelongsTo
    {
        return $this->belongsTo(Saison::class);
    }
}
