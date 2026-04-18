<div align="center">

<svg width="100%" height="180" viewBox="0 0 1200 180" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Lyon Palme Inscription">
  <defs>
    <linearGradient id="lp" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#3F3466"/>
      <stop offset="50%" stop-color="#5B4B8A"/>
      <stop offset="100%" stop-color="#5DD9D2"/>
    </linearGradient>
  </defs>
  <rect x="0" y="0" width="1200" height="180" rx="20" fill="url(#lp)"/>
  <text x="60" y="92" font-size="56" font-family="Verdana, sans-serif" font-weight="700" fill="white">Lyon Palme Inscription</text>
  <text x="62" y="132" font-size="24" font-family="Verdana, sans-serif" fill="rgba(255,255,255,0.9)">Gestion simple des adherents, certificats et cotisations</text>
</svg>

</div>

Un projet scolaire concret: quand les tableaux Excel du club commencent a casser, on remet de l ordre avec une app claire, rapide et compréhensible.

![Laravel](https://img.shields.io/badge/Laravel-13-red)
![PHP](https://img.shields.io/badge/PHP-8.3-blue)
![Tailwind](https://img.shields.io/badge/Tailwind-4-06B6D4)
![License](https://img.shields.io/badge/License-MIT-green)
![BTS SIO](https://img.shields.io/badge/BTS%20SIO-SLAM%202026-purple)

## Pourquoi ce projet

Le club Lyon Palme suit des inscriptions, des certificats medicaux et des cotisations.
Au depart, tout passait par des fichiers manuels. Quand les infos se multiplient (retards de paiement, certificats expirants, adherents archives), le suivi devient fragile.

Cette application apporte un workflow simple pour la secretaire:

- visualiser rapidement ce qui est a jour ou en retard
- enregistrer les paiements sans systeme de paiement en ligne
- garder un historique propre et exploitable

## Stack technique

| Couche | Choix |
|---|---|
| Backend | Laravel 13, PHP 8.3 |
| Frontend | Blade, Tailwind CSS 4, Alpine.js |
| Base locale | SQLite |
| Export | maatwebsite/excel |
| Tests | PHPUnit |

## Installation (5 etapes)

1. Cloner le repo puis installer les dependances:
```bash
composer install
npm install
```
2. Configurer l environnement:
```bash
cp .env.example .env
php artisan key:generate
```
3. Preparer la base locale:
```bash
touch database/database.sqlite
php artisan migrate:fresh --seed
```
4. Lier le stockage public:
```bash
php artisan storage:link
```
5. Lancer l application:
```bash
php artisan serve
npm run dev
```

## Captures d ecran

Les captures sont a placer dans [`docs/screenshots/`](docs/screenshots/).

## Comptes de demo

- Secretaire: `secretaire@lyonpalme.com` / `SecretLyon2026!`
- Admin: `admin@lyonpalme.com` / `AdminLyon2026!`
- Adherent (exemple): `adherent01@lyonpalme.test` / `AdherentLyon2026!`

## Perimetre

### Inclus

- Auth secretaire (connexion + changement mot de passe)
- CRUD adherents (creation, edition, archivage/reactivation)
- Vue certificats medicaux (statuts + export)
- Vue cotisations/paiements (statuts + export)

### Non inclus

- Espace nageur / annuaire / trombinoscope
- Planning entrainements et competitions
- Chiffrement avance type AES-256 metier
- Integrations de paiement reelles (Stripe, HelloAsso API, etc.)
- Roles/permissions avances via packages externes

## Securite (version scolaire)

- Mots de passe haches
- Sessions Laravel standards
- Consentement RGPD trace (`rgpd_accepte`, date, IP)
- Archivage logique des adherents (pas de suppression metier)

## Deploiement

Le projet est prevu pour un deploiement sous `/LyonPalme` via configuration Nginx (Option A).

Voir la documentation: [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md)

## Roadmap

- ✅ US4-US11: flux principal de gestion club
- 🚧 Captures d ecran finales du projet
- ❌ Fonctions hors scope (espace nageur, planning, paiement en ligne)

🏊‍♂️ Cherif Hammani — BTS SIO SLAM 2026  
Portfolio: [www.cherifhammani.fr](https://www.cherifhammani.fr)
