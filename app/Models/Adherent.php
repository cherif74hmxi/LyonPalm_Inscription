<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Adherent extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'adherents';

    protected $fillable = [
        'nom',
        'prenom',
        'date_naissance',
        'sexe',
        'email',
        'password',
        'adresse',
        'code_postal',
        'ville',
        'telephone',
        'mobile',
        'contact_urgence_nom',
        'contact_urgence_telephone',
        'photo',
        'statut',
        'rgpd_accepte',
        'rgpd_accepte_le',
        'rgpd_ip',
        'archive_le',
        'representant_legal_id',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'rgpd_accepte' => 'boolean',
        'rgpd_accepte_le' => 'datetime',
        'archive_le' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function representantLegal(): BelongsTo
    {
        return $this->belongsTo(RepresentantLegal::class, 'representant_legal_id');
    }

    public function certificatsMedicaux(): HasMany
    {
        return $this->hasMany(CertificatMedical::class);
    }

    public function dernierCertificat(): HasOne
    {
        return $this->hasOne(CertificatMedical::class)->latestOfMany('date_expiration');
    }

    public function adhesions(): HasMany
    {
        return $this->hasMany(Adhesion::class);
    }

    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeArchives($query)
    {
        return $query->where('statut', 'archive');
    }

    public function scopeMineurs($query)
    {
        return $query->whereDate('date_naissance', '>', now()->subYears(18)->toDateString());
    }

    public function scopeMajeurs($query)
    {
        return $query->whereDate('date_naissance', '<=', now()->subYears(18)->toDateString());
    }

    public function scopeRecherche($query, ?string $terme)
    {
        if (! $terme) {
            return $query;
        }

        return $query->where(function ($inner) use ($terme) {
            $like = '%'.$terme.'%';
            $inner
                ->where('nom', 'like', $like)
                ->orWhere('prenom', 'like', $like)
                ->orWhere('email', 'like', $like);
        });
    }

    public function estMineur(?Carbon $reference = null): bool
    {
        $reference ??= now();

        return Carbon::parse($this->date_naissance)->diffInYears($reference) < 18;
    }

    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom.' '.$this->nom);
    }

    public function getAgeAttribute(): int
    {
        return Carbon::parse($this->date_naissance)->age;
    }

    public function getCertificatValide(): ?CertificatMedical
    {
        return $this->certificatsMedicaux()
            ->whereDate('date_expiration', '>=', now()->toDateString())
            ->orderByDesc('date_expiration')
            ->first();
    }
}
