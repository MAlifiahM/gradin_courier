## Tech Stack

- PHP
- Laravel
- PostgreSQL
- Docker Compose
- PHPUnit


## Requirements

- PHP
- Composer
- Docker
- Docker Compose
- PHP PostgreSQL extension (pdo_pgsql)

## Postman Collection

A Postman collection is provided to simplify API testing.

Collection location:
`docs/postman/gradin_courier.postman_collection.json`

### How to use

1. Open Postman.
2. Click Import.
3. Select the provided JSON collection.
4. Configure the `base_url` variable.
5. Run the requests.

## Installation

Clone repository:

```bash
git clone git@github.com:MAlifiahM/gradin_courier.git

cd gradin_courier
```

Install dependencies:
```bash
composer install
```

Copy environment configuration:
```bash
cp .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

## Environment Configuration

Configure the following variables in `.env`:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=database name
DB_USERNAME=username
DB_PASSWORD=password
```

## Database Setup

Start PostgreSQL:
```bash
docker compose up -d
```

Run migrations:

```bash
php artisan migrate
```

## Running the Application
```bash
php artisan serve
```

## Route
```http://localhost:8000/api```
