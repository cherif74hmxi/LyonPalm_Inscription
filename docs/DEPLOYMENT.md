# Deploiement VPS (Nginx + PHP-FPM + Laravel)

Ce guide couvre un deploiement de production sur VPS Ubuntu avec MySQL.

## 1) Prerequis serveur

- Ubuntu 22.04/24.04
- Nginx
- PHP-FPM 8.3+ (extensions: `mbstring`, `xml`, `curl`, `zip`, `gd`, `pdo_mysql`, `bcmath`, `intl`)
- MySQL/MariaDB
- Composer
- Node.js 20+ et npm

## 2) Installation de l application

```bash
sudo mkdir -p /var/www/LyonPalmInscription
sudo chown -R $USER:$USER /var/www/LyonPalmInscription
cd /var/www/LyonPalmInscription
```

Copier ensuite le projet dans ce dossier puis executer:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
cp .env.example .env
php artisan key:generate
```

## 3) Configuration `.env` production

Exemple:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ton-domaine.tld

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=LyonPalm
DB_USERNAME=ton_user
DB_PASSWORD=ton_password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Puis:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Permissions recommandees:

```bash
sudo chown -R www-data:www-data /var/www/LyonPalmInscription/storage /var/www/LyonPalmInscription/bootstrap/cache
sudo chmod -R 775 /var/www/LyonPalmInscription/storage /var/www/LyonPalmInscription/bootstrap/cache
```

## 4) Nginx (option racine du domaine)

Exemple fichier `/etc/nginx/sites-available/lyonpalme`:

```nginx
server {
    listen 80;
    server_name ton-domaine.tld www.ton-domaine.tld;

    root /var/www/LyonPalmInscription/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Activation:

```bash
sudo ln -s /etc/nginx/sites-available/lyonpalme /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 5) Nginx (option sous-dossier `/LyonPalme`)

Si tu deployes sous `https://ton-domaine.tld/LyonPalme`:

- Mettre `APP_URL=https://ton-domaine.tld/LyonPalme`
- Garder les routes Laravel sans prefixe global

Exemple:

```nginx
location /LyonPalme {
    alias /var/www/LyonPalmInscription/public;
    try_files $uri $uri/ /index.php?$query_string;

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $request_filename;
    }
}
```

## 6) HTTPS (LetsEncrypt)

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d ton-domaine.tld -d www.ton-domaine.tld
```

## 7) Cron scheduler

```bash
* * * * * cd /var/www/LyonPalmInscription && php artisan schedule:run >> /dev/null 2>&1
```

## 8) Verification post-deploiement

```bash
php artisan about
php artisan route:list
php artisan test --filter=SmokeTest
```

A verifier dans le navigateur:

- `/` (page d accueil avec choix adherent / secretaire-admin)
- `/login` (espace interne)
- `/espace-adherent/login` (espace adherent)
- Logo charge via `/logo.svg`
