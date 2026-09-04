# Laracast Forum API

A production-oriented **REST API** for a community forum, built with **Laravel 8**. Members can create discussion *threads*, post *answers*, mark a *best answer*, subscribe to threads for reply notifications, earn *score* points and climb public *leaderboards*. Moderators manage *channels* and blocked users are prevented from posting.

## About

Laracast Forum is a JSON-only backend designed to power a Q&A style forum. It exposes a versioned API under `/api/v1` and is fully containerized for local development. The project follows a **Controller → Repository → Model** layering, keeps business queries in repositories, and uses Laravel **Gates**, **Sanctum** authentication and **Spatie permissions** for authorization.

### Features

- 🔐 Authentication with Laravel Sanctum
- 🗂 Channels managed by moderators (`channel management` permission)
- 💬 Threads with human-friendly slugs and paginated listing
- ✍️ Answers with ownership-based editing/deletion
- ⭐ Best-answer marking per thread
- 🔔 Database notifications to thread subscribers on new replies
- 🏆 Score & leaderboards (10 points per useful answer)
- 🚫 Blocked-user protection middleware on write endpoints
- 🐳 Dockerized local stack (nginx + PHP 7.4-FPM + MySQL 8)

## Tech Stack & Tags

`php` `laravel` `mysql` `docker` `nginx` `rest-api` `forum` `laravel-sanctum` `spatie-permission` `repository-pattern`

## Repository Layout

```
.
├── docker-compose.yml     # nginx + php-fpm + mysql dev stack
├── Dockerfile             # PHP 7.4-FPM runtime image
├── nginx/
│   └── default.conf       # nginx vhost config
└── src/                   # Laravel application (the forum API)
    ├── app/
    │   ├── Http/Controllers/API/v1/{Auth,Channel,Thread,User}
    │   ├── Http/Middleware/UserBlock.php
    │   ├── Models/        # User, Channel, Thread, Answer, Subscribe
    │   ├── Notifications/ # NewReplySubmitted
    │   └── repositories/  # Repository layer
    ├── config/
    ├── database/          # Migrations, factories, seeders
    ├── routes/
    │   ├── api.php
    │   └── v1/            # Versioned route groups
    └── tests/
```

## Quick Start (Docker)

Requirements: Docker with the Compose plugin.

```bash
# 1. Start the stack (nginx, php-fpm, mysql)
docker compose up -d --build

# 2. Install PHP dependencies
docker compose run --rm composer install --ignore-platform-reqs

# 3. Configure the environment
cp src/.env.example src/.env

# 4. Generate the application key and fix storage permissions
docker compose exec php php artisan key:generate
docker compose exec php chown -R www-data:www-data storage bootstrap/cache

# 5. Build the schema and seed roles/permissions
docker compose exec php php artisan migrate --seed
```

The API is then available at `http://localhost/api/v1`.

### Making the first user an administrator

```bash
docker compose exec php php artisan tinker --execute="\App\Models\User::find(1)->assignRole('Super Admin');"
```

> Super Admins bypass all permission checks (see `AuthServiceProvider`). Channel
> management is restricted to the `channel management` permission / Super Admin role.

## API Reference

All routes are prefixed with `/api/v1`.

### Auth (`/auth`)

| Method | Endpoint   | Auth | Body / Notes                          |
|--------|------------|------|---------------------------------------|
| POST   | `/register`| –    | `name`, `email`, `password`           |
| POST   | `/login`   | –    | `email`, `password` → returns user    |
| GET    | `/user`    | ✓    | Current user + unread notifications   |
| POST   | `/logout`  | ✓    | Logs the current user out             |

### Users (`/users`)

| Method | Endpoint       | Auth | Notes                          |
|--------|----------------|------|--------------------------------|
| GET    | `/leaderboards`| –    | Users ordered by score (paginated) |

### Channels (`/channel`)

| Method | Endpoint  | Auth + Permission        | Body / Notes            |
|--------|-----------|--------------------------|-------------------------|
| GET    | `/all`    | public                   | List all channels       |
| POST   | `/create` | `can:channel management` | `name`                  |
| PUT    | `/update` | `can:channel management` | `id`, `name`            |
| DELETE | `/delete` | `can:channel management` | `id`                    |

### Threads (`/threads`)

| Method | Endpoint           | Auth | Body / Notes                                   |
|--------|--------------------|------|------------------------------------------------|
| GET    | `/`                | –    | Paginated available threads (`flag = 1`)       |
| POST   | `/`                | ✓    | `title`, `content`, `channel_id`               |
| GET    | `/{slug}`          | –    | Thread detail by slug                          |
| PUT    | `/{thread}`        | ✓    | Owner: `title`, `content`, `channel_id`; `best_answer_id` to mark best answer |
| DELETE | `/{thread}`        | ✓    | Owner only                                     |

### Answers & Subscriptions (under `/threads`)

| Method | Endpoint                     | Auth | Body / Notes                    |
|--------|------------------------------|------|---------------------------------|
| GET    | `/answers`                   | –    | All answers (paginated)         |
| POST   | `/answers`                   | ✓    | `content`, `thread_id`          |
| PUT    | `/answers/{answer}`          | ✓    | Owner: `content`                |
| DELETE | `/answers/{answer}`          | ✓    | Owner only                      |
| POST   | `/{thread}/subscribe`        | ✓    | Subscribe to a thread           |
| POST   | `/{thread}/unsubscribe`      | ✓    | Unsubscribe from a thread       |

### Authentication model

Routes marked **Auth** are protected with `auth:sanctum` and the `user-block`
middleware. Authentication is handled by **Laravel Sanctum**: stateful
session/cookie requests work out of the box; to issue bearer tokens for mobile
clients call `$user->createToken(...)` on login.

## Tests

```bash
# inside the php container (or locally with PHP 7.4)
docker compose exec php vendor/bin/phpunit
```

## Deployment

See **[DEPLOYMENT.md](DEPLOYMENT.md)** for the production-ready deployment
guide (hardening, migrations, caching, TLS, backups, monitoring).

## Contributing

1. Fork the repository.
2. Create a feature branch (`git checkout -b feat/my-feature`).
3. Commit your changes and open a pull request.

## License

[MIT](LICENSE) © Farhad Hosseini
