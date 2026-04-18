<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Adhesion extends Model
{
    protected $table = 'adhesions';

    public $timestamps = false;

    protected $fillable = [
        'adherent_id',
        'saison_id',
        'type_adhesion_id',
        'montant_total',
        'montant_paye',
    ];

    protected $casts = [
        'montant_total' => 'decimal:2',
        'montant_paye' => 'decimal:2',
    ];

    public function adherent(): BelongsTo
    {
        return $this->belongsTo(Adherent::class);
    }

    public function saison(): BelongsTo
    {
        return $this->belongsTo(Saison::class);
    }

    public function typeAdhesion(): BelongsTo
    {
        return $this->belongsTo(TypeAdhesion::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function getSoldeAttribute(): float
    {
        return max(0, (float) $this->montant_total - (float) $this->montant_paye);
    }

    public function getStatutPaiementAttribute(): string
    {
        if ($this->solde <= 0.00001) {
            return 'a_jour';
        }

        if ((float) $this->montant_paye > 0) {
            return 'partiel';
        }

        return 'impaye';
    }
}
