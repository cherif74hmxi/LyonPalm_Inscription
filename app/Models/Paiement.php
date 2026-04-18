<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    protected $table = 'paiements';

    public $timestamps = false;

    public const MODES = ['Especes', 'Cheque', 'Virement', 'Carte', 'HelloAsso'];

    protected $fillable = [
        'adhesion_id',
        'montant',
        'mode',
        'date_paiement',
        'remarques',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'date',
    ];

    public function adhesion(): BelongsTo
    {
        return $this->belongsTo(Adhesion::class);
    }
}
