@php
    $editing = isset($adherent);
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <section class="card p-5">
        <h2 class="mb-4 text-lg font-semibold">Identite</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="label" for="nom">Nom</label>
                <input id="nom" name="nom" class="input" value="{{ old('nom', $adherent->nom ?? '') }}" required />
            </div>
            <div>
                <label class="label" for="prenom">Prenom</label>
                <input id="prenom" name="prenom" class="input" value="{{ old('prenom', $adherent->prenom ?? '') }}" required />
            </div>
            <div>
                <label class="label" for="date_naissance">Date de naissance</label>
                <input id="date_naissance" type="date" name="date_naissance" class="input" value="{{ old('date_naissance', isset($adherent) ? $adherent->date_naissance?->format('Y-m-d') : '') }}" required />
            </div>
            <div>
                <label class="label" for="sexe">Sexe</label>
                <select id="sexe" name="sexe" class="input">
                    <option value="">Selectionner</option>
                    @foreach (['M', 'F', 'Autre'] as $value)
                        <option value="{{ $value }}" @selected(old('sexe', $adherent->sexe ?? '') === $value)>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="email">Email</label>
                <input id="email" type="email" name="email" class="input" value="{{ old('email', $adherent->email ?? '') }}" required />
            </div>
            <div>
                <label class="label" for="mobile">Mobile</label>
                <input id="mobile" name="mobile" class="input" value="{{ old('mobile', $adherent->mobile ?? '') }}" />
            </div>
        </div>
    </section>

    <section class="card p-5">
        <h2 class="mb-4 text-lg font-semibold">Coordonnees</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="label" for="adresse">Adresse</label>
                <input id="adresse" name="adresse" class="input" value="{{ old('adresse', $adherent->adresse ?? '') }}" required />
            </div>
            <div>
                <label class="label" for="code_postal">Code postal</label>
                <input id="code_postal" name="code_postal" class="input" value="{{ old('code_postal', $adherent->code_postal ?? '') }}" required />
            </div>
            <div>
                <label class="label" for="ville">Ville</label>
                <input id="ville" name="ville" class="input" value="{{ old('ville', $adherent->ville ?? '') }}" required />
            </div>
            <div>
                <label class="label" for="telephone">Telephone fixe</label>
                <input id="telephone" name="telephone" class="input" value="{{ old('telephone', $adherent->telephone ?? '') }}" />
            </div>
        </div>
    </section>

    <section class="card p-5">
        <h2 class="mb-4 text-lg font-semibold">Contact urgence</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="label" for="contact_urgence_nom">Nom</label>
                <input id="contact_urgence_nom" name="contact_urgence_nom" class="input" value="{{ old('contact_urgence_nom', $adherent->contact_urgence_nom ?? '') }}" required />
            </div>
            <div>
                <label class="label" for="contact_urgence_telephone">Telephone</label>
                <input id="contact_urgence_telephone" name="contact_urgence_telephone" class="input" value="{{ old('contact_urgence_telephone', $adherent->contact_urgence_telephone ?? '') }}" required />
            </div>
        </div>
    </section>

    <section class="card p-5">
        <h2 class="mb-1 text-lg font-semibold">Representant legal (si mineur)</h2>
        <p class="mb-4 text-sm text-slate-500">Remplir ces informations si l adherent a moins de 18 ans.</p>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="label" for="representant_nom">Nom</label>
                <input id="representant_nom" name="representant_nom" class="input" value="{{ old('representant_nom', $adherent->representantLegal->nom ?? '') }}" />
            </div>
            <div>
                <label class="label" for="representant_prenom">Prenom</label>
                <input id="representant_prenom" name="representant_prenom" class="input" value="{{ old('representant_prenom', $adherent->representantLegal->prenom ?? '') }}" />
            </div>
            <div>
                <label class="label" for="representant_telephone">Telephone</label>
                <input id="representant_telephone" name="representant_telephone" class="input" value="{{ old('representant_telephone', $adherent->representantLegal->telephone ?? '') }}" />
            </div>
            <div>
                <label class="label" for="representant_mobile">Mobile</label>
                <input id="representant_mobile" name="representant_mobile" class="input" value="{{ old('representant_mobile', $adherent->representantLegal->mobile ?? '') }}" />
            </div>
            <div>
                <label class="label" for="representant_email">Email</label>
                <input id="representant_email" type="email" name="representant_email" class="input" value="{{ old('representant_email', $adherent->representantLegal->email ?? '') }}" />
            </div>
            <div>
                <label class="label" for="representant_lien_parental">Lien parental</label>
                <input id="representant_lien_parental" name="representant_lien_parental" class="input" value="{{ old('representant_lien_parental', $adherent->representantLegal->lien_parental ?? '') }}" />
            </div>
        </div>
    </section>

    <section class="card p-5">
        <h2 class="mb-4 text-lg font-semibold">Photo</h2>

        @if ($editing && $adherent->photo)
            <img src="{{ asset('storage/'.$adherent->photo) }}" alt="Photo adherent" class="mb-3 h-24 w-24 rounded-xl object-cover" />
        @endif

        <input type="file" name="photo" accept="image/*" class="input" />
    </section>

    <section class="card p-5">
        <h2 class="mb-4 text-lg font-semibold">Consentement RGPD</h2>
        <label class="flex items-start gap-3 text-sm text-slate-700">
            <input type="checkbox" name="rgpd_accepte" value="1" class="mt-0.5 rounded border-slate-300" @checked(old('rgpd_accepte', $adherent->rgpd_accepte ?? false)) {{ $editing ? '' : 'required' }} />
            <span>Je confirme le consentement de l adherent pour le traitement des donnees dans le cadre des inscriptions.</span>
        </label>
    </section>

    <div class="flex flex-wrap gap-3">
        <button type="submit" class="btn-primary">{{ $submitLabel }}</button>
        <a href="{{ route('adherents.index') }}" class="btn-secondary">Annuler</a>
    </div>
</form>
