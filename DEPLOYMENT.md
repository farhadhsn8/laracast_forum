# Production Deployment Guide

This guide walks through deploying the Laracast Forum API to a production
server (single host) using **nginx + PHP-FPM 7.4 + MySQL 8**, mirroring the
Docker runtime of this repository.

> The bundled `docker-compose.yml` is a **development** stack. For production,
> prefer installing PHP-FPM/nginx natively (as below) or wrap the images in a
> hardened compose/stack with proper secrets, health-checks and TLS.

## 1. Server prerequisites

- Ubuntu 22.04 / Debian 12 (or compatible)
- PHP `7.4` with extensions: `php7.4-fpm`, `php7.4-mysql`, `php7.4-mbstring`,
  `php7.4-xml`, `php7.4-curl`, `php7.4-zip`, `php7.4-bcmath`, `php7.4-tokenizer`
- Composer 2.x
- MySQL 8.x
- nginx

## 2. Ship the code

```bash
sudo mkdir -p /var/www/laracast_forum
sudo chown -R "$USER":"$USER" /var/www/laracast_forum
git clone git@github.com:farhadhsn8/laracast_forum.git /var/www/laracast_forum

cd /var/www/laracast_forum/src
composer install --no-dev --optimize-autoloader --no-interaction
```

## 3. Environment configuration

```bash
cp .env.example .env
php artisan key:generate
```

Set the following values (never commit `.env`):

| Variable            | Production value                          |
|---------------------|-------------------------------------------|
| `APP_ENV`           | `production`                              |
| `APP_DEBUG`         | `false`                                   |
| `APP_URL`           | `https://forum.example.com`               |
| `APP_KEY`           | generated                                 |
| `LOG_CHANNEL`       | `stack`                                   |
| `DB_HOST`           | `127.0.0.1`                               |
| `DB_DATABASE`       | strong database name                      |
| `DB_USERNAME`       | non-root user with least privilege        |
| `DB_PASSWORD`       | strong password                           |
| `CACHE_DRIVER`      | `redis` or `file`                         |
| `SESSION_DRIVER`    | `redis` or `file`                         |
| `QUEUE_CONNECTION`  | `sync` (no background workers needed yet) |
| `MAIL_MAILER`       | `smtp` + credentials                      |
| `MAIL_FROM_ADDRESS` | real from-address                         |

Hardening tips:

- `APP_DEBUG=false` and `LOG_LEVEL=error` in production.
- Use a dedicated MySQL user (never `root`) with credentials only for this
  database.
- Rotate `APP_KEY` — losing it invalidates all encrypted data.

## 4. Database setup

```sql
CREATE DATABASE laracast_forum CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'forum'@'localhost' IDENTIFIED BY 'CHANGE_ME';
GRANT ALL PRIVILEGES ON laracast_forum.* TO 'forum'@'localhost';
FLUSH PRIVILEGES;
```

```bash
php artisan migrate --force
php artisan db:seed --force
```

`db:seed` creates the roles (`Super Admin`, `User`) and permissions
(`user management`, `channel management`, `thread management`,
`answers management`).

### Create the administrator

```bash
php artisan tinker --execute="\App\Models\User::find(1)->assignRole('Super Admin');"
```

Super Admins bypass all authorization checks. Regular users can subscribe,
post threads and answers, and earn score.

## 5. Storage & caches

```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

sudo chown -R www-data:www-data storage bootstrap/cache
```

> Run `php artisan config:clear`/`route:clear`/`view:clear` after every deploy
> that changes config, routes or views.

## 6. PHP-FPM pool

`/etc/php/7.4/fpm/pool.d/www.conf` — run FPM as `www-data` and set a
reasonable memory limit:

```ini
user = www-data
group = www-data
listen = /run/php/php7.4-fpm.sock
pm.max_children = 10
pm.start_servers = 3
pm.min_spare_servers = 1
pm.max_spare_servers = 5
php_admin_value[memory_limit] = 256M
```

## 7. nginx vhost

`/etc/nginx/sites-available/laracast_forum`:

```nginx
server {
    listen 80;
    server_name forum.example.com;

    root /var/www/laracast_forum/src/public;
    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php7.4-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff2?)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/laracast_forum /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

## 8. TLS (HTTPS)

Use Certbot:

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d forum.example.com
```

Certbot rewrites the vhost to HTTPS and configures auto-renewal.

## 9. Backups & restores

MySQL (daily cron, e.g. `0 2 * * *`):

```bash
mysqldump -u forum -p laracast_forum | gzip > /backups/laracast_forum_$(date +\%F).sql.gz
find /backups -name 'laracast_forum_*.sql.gz' -mtime +14 -delete
```

Restore:

```bash
gunzip < laracast_forum_2026-09-01.sql.gz | mysql -u forum -p laracast_forum
```

Also back up `src/.env` (contains `APP_KEY`) and `src/storage`.

## 10. Monitoring & logging

- Tail logs: `tail -f /var/www/laracast_forum/src/storage/logs/laravel.log`
- Monitor PHP-FPM and MySQL with `systemctl status php7.4-fpm mysql`.
- Add uptime/alerting (e.g. UptimeRobot) against `https://forum.example.com/api/v1/channel/all`.

## 11. Deploy checklist

- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `APP_URL` is the HTTPS origin
- [ ] `APP_KEY` generated and backed up
- [ ] Non-root DB user + strong passwords
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `php artisan migrate --force`
- [ ] `php artisan config:cache`, `route:cache`, `view:cache`
- [ ] Storage/`bootstrap/cache` owned by `www-data`
- [ ] TLS valid and redirecting HTTP → HTTPS
- [ ] Nightly DB backups verified with a restore drill
