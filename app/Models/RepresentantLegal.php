<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RepresentantLegal extends Model
{
    protected $table = 'representants_legaux';

    public $timestamps = false;

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'mobile',
        'email',
        'lien_parental',
    ];

    public function adherent(): HasOne
    {
        return $this->hasOne(Adherent::class, 'representant_legal_id');
    }
}
