# Mini Library

A Laravel learning project: a library management system covering authentication,
roles/authorization, CRUD, search/filtering, borrow/return/reservation workflows,
events & queued mail, a scheduled overdue-loan check, and a JSON API.


## Tech Stack

- Laravel 12, PHP 8.3
- PostgreSQL
- Blade + Tailwind CSS
- Laravel Sanctum (API authentication)
- Laravel Pint (code style)

## Setup

1. Clone the repo and install dependencies:
   ```bash
   composer install
   npm install
   ```

2. Copy the environment file and generate an app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Configure your database in `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=mini_library
   DB_USERNAME=your_username
   DB_PASSWORD=
   ```
   Create the database first, e.g.:
   ```sql
   CREATE DATABASE mini_library;
   ```

4. Run migrations and seed sample data:
   ```bash
   php artisan migrate --seed
   ```

5. Build frontend assets and start the app:
   ```bash
   composer run dev
   ```
   This runs the PHP server, queue listener, and Vite dev server together.
   Visit `http://127.0.0.1:8000`.

## Seeded Test Accounts

| Role      | Email                        | Password |
|-----------|-------------------------------|----------|
| Admin     | admin@mini-library.test       | password |
| Librarian | librarian@mini-library.test   | password |
| Member    | any seeded member — find one via `php artisan tinker` → `User::where('role','member')->first()` | password |

`admin` and `librarian` currently share identical permissions — both satisfy
every "admin/librarian can manage books, authors, categories" requirement in
the spec. They exist as separate role labels rather than a single role because
the spec lists them as distinct roles, even though no permission in the spec
distinguishes between them.

## Roles

- **admin / librarian** — manage books, authors, categories; view and manage
  any member's loans and reservations
- **member** — browse/search the catalog; borrow, return, and reserve books;
  view and manage only their own loans and reservations

## Queue Worker

Emails (borrow confirmations, availability notices, overdue reminders) are
sent via queued jobs. Run a worker in a separate terminal for them to process:

```bash
php artisan queue:work
```

Without this running, queued jobs sit in the `jobs` table unprocessed — the
app still works normally, emails simply won't send until a worker picks them up.

## Scheduler

The overdue-loan check (`loans:check-overdue`) is scheduled to run daily via
`routes/console.php`. To simulate this locally:

```bash
php artisan schedule:work
```

**Production setup:** add a single cron entry to the server:
```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```
Laravel internally determines which scheduled tasks are actually due each
minute — a separate cron entry per task is not needed. A queue worker must
also run continuously in production, ideally under a process manager such as
Supervisor:
```
php artisan queue:work --daemon
```

## Running Tests

```bash
php artisan test
```

The test database uses in-memory SQLite by default (configured in
`phpunit.xml`) and requires the `pdo_sqlite` PHP extension to be enabled.

## API

Base URL: `/api`. Public endpoints (`GET /books`, `GET /books/{id}`) require
no authentication. Authenticated endpoints require a Sanctum bearer token.

```
GET    /api/books
GET    /api/books/{book}
POST   /api/books/{book}/borrow   (auth required)
POST   /api/loans/{loan}/return   (auth required)
GET    /api/my-loans              (auth required)
```

Generate a test token via tinker:
```php
$user = \App\Models\User::first();
$user->createToken('test')->plainTextToken;
```

## Demo Flow

1. Log in as a member → browse `/books`, search/filter/sort the catalog.
2. Borrow an available book → check "My Loans" → confirm a confirmation email
   appears once the queue worker processes it (`storage/logs/laravel.log` if
   `MAIL_MAILER=log`).
3. Log in as a second member → reserve that same now-unavailable book →
   confirm the reservation queue count updates.
4. Log back in as the first member → return the book → confirm the second
   member's reservation is marked fulfilled and they receive an availability
   email.
5. Log in as librarian/admin → manage books/authors/categories (create, edit,
   delete) → confirm a member account cannot see or use those same controls.
6. In tinker, backdate a loan's `due_at` → run
   `php artisan loans:check-overdue` → confirm it's marked overdue and a
   reminder email queues.
7. Hit `/api/books` in a browser or curl, then an authenticated
   `/api/my-loans` call with a Sanctum token.

## Out of Scope

Payment/fine collection, barcode scanning, multi-branch libraries, complex
inventory management, real production deployment, advanced frontend SPA
behavior.
