# Laravel Social Media Website
Laravel Social Media Website built with Inertia Vue.js. 

## Installation with docker
only that for the moment is not working
#### 1. Clone the project
#### 2. Run `composer install`
Navigate into project folder using terminal and run

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

#### 3. Copy `.env.example` into `.env`

```bash
cp .env.example .env
```

#### 4. Start the project in detached mode

```bash
./vendor/bin/sail up -d
```
From now on whenever you want to run artisan command you should do this from the container. <br>
Access to the docker container
```bash
./vendor/bin/sail bash
```

#### 5. Set encryption key

```bash
php artisan key:generate --ansi
```

#### 6. Run migrations

```bash
php artisan migrate
```