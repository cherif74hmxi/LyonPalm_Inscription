<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificatMedical extends Model
{
    use HasFactory;

    protected $table = 'certificats_medicaux';

    protected $fillable = [
        'adherent_id',
        'date_emission',
        'date_expiration',
        'fichier',
        'restrictions',
    ];

    public $timestamps = false;

    protected $casts = [
        'date_emission' => 'date',
        'date_expiration' => 'date',
    ];

    public function adherent(): BelongsTo
    {
        return $this->belongsTo(Adherent::class);
    }

    public function getStatutAttribute(): string
    {
        $today = now()->startOfDay();

        if ($this->date_expiration->lt($today)) {
            return 'expire';
        }

        if ($this->date_expiration->lte($today->copy()->addDays(30))) {
            return 'expire_bientot';
        }

        return 'valide';
    }

    public function getJoursRestantsAttribute(): int
    {
        $expiration = $this->date_expiration->copy()->startOfDay();
        $days = (int) now()->startOfDay()->diffInDays($expiration, false);

        return max(0, $days);
    }

    public function getQuestionnaireSanteRequisAttribute(): bool
    {
        return $this->date_emission->lte(Carbon::now()->subYear());
    }

    public function scopeValides($query)
    {
        return $query->whereDate('date_expiration', '>', now()->addDays(30)->toDateString());
    }

    public function scopeExpireBientot($query)
    {
        return $query->whereBetween('date_expiration', [
            now()->toDateString(),
            now()->addDays(30)->toDateString(),
        ]);
    }

    public function scopeExpires($query)
    {
        return $query->whereDate('date_expiration', '<', now()->toDateString());
    }
}
