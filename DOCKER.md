# AI Task Manager - Docker Setup

## Quick Start

### 1. Clone and Setup Environment
```bash
# Copy environment file
cp .env.example .env

# Update .env with your configuration
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=your_secure_password
REDIS_PASSWORD=your_redis_password
```

### 2. Build and Start Containers
```bash
# Build the Docker image
docker-compose build

# Start all services
docker-compose up -d

# Verify all containers are running
docker-compose ps
```

### 3. Initialize Application
```bash
# Install PHP dependencies
docker-compose exec app composer install

# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate

# Create storage symlink
docker-compose exec app php artisan storage:link
```

### 4. Access Application
- **Application**: http://localhost
- **Database**: localhost:5432
- **Redis**: localhost:6379

## Services

### app (PHP-FPM)
- PHP 8.3-FPM application container
- Port: 9000 (internal)
- Handles all Laravel application requests

### nginx (Web Server)
- Nginx web server
- Port: 80 (HTTP)
- Routes requests to PHP-FPM
- Serves static assets

### postgres (Database)
- PostgreSQL 16
- Port: 5432
- Stores application data

### redis (Cache & Queue)
- Redis 7
- Port: 6379
- Handles caching and job queuing
- Session storage

### queue (Queue Worker)
- Processes jobs from Redis queue
- Listens to 'ai' and 'default' queues
- Auto-retries up to 3 times
- 90-second timeout per job

### scheduler (Task Scheduler)
- Runs Laravel's scheduled tasks
- Executes every minute
- Handles recurring jobs

## Common Commands

### View Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f queue
docker-compose logs -f scheduler
```

### Run Artisan Commands
```bash
docker-compose exec app php artisan {command}
```

### Stop and Start
```bash
# Stop containers
docker-compose stop

# Start containers
docker-compose start

# Restart containers
docker-compose restart

# Remove containers (keep volumes)
docker-compose down

# Remove everything (including volumes)
docker-compose down -v
```

### Database Management
```bash
# Access PostgreSQL CLI
docker-compose exec postgres psql -U laravel -d laravel

# Backup database
docker-compose exec postgres pg_dump -U laravel laravel > backup.sql

# Restore database
docker-compose exec postgres psql -U laravel laravel < backup.sql
```

### Queue Management
```bash
# Monitor queue jobs
docker-compose exec app php artisan queue:monitor ai,default

# Retry failed jobs
docker-compose exec app php artisan queue:retry --all

# Clear failed jobs
docker-compose exec app php artisan queue:flush
```

## Environment Variables

Key variables in `.env`:
```
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel

REDIS_HOST=redis
REDIS_PASSWORD=
REDIS_PORT=6379

QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
CACHE_DRIVER=redis
```

## Debugging

### Check Container Status
```bash
docker-compose ps
docker-compose logs {service_name}
```

### Health Checks
Each service has health checks enabled:
- `docker-compose ps` shows health status
- Check container health: `docker inspect {container_name}`

### Connect to Container Shell
```bash
docker-compose exec app bash
docker-compose exec postgres bash
docker-compose exec redis redis-cli
```

## Performance Optimization

- PHP OPCache enabled with 256MB memory
- Nginx gzip compression enabled
- Redis caching for sessions and queries
- Database connection pooling via environment
- Static asset caching (1 year expiry)

## Production Considerations

Before deploying to production:

1. **Security**
   - Update all passwords in `.env`
   - Enable HTTPS (add SSL cert to nginx)
   - Set `APP_DEBUG=false`
   - Use strong `REDIS_PASSWORD`

2. **Performance**
   - Increase PHP `memory_limit` if needed
   - Scale queue workers with multiple instances
   - Use persistent volume storage

3. **Monitoring**
   - Set up centralized logging
   - Monitor queue jobs
   - Track failed jobs
   - Monitor Redis memory usage

4. **Backups**
   - Regular PostgreSQL backups
   - Backup application files
   - Backup Redis persistence

## Troubleshooting

### Connection Refused
- Ensure Docker daemon is running
- Check if ports are not already in use
- Verify containers are healthy: `docker-compose ps`

### Permission Denied
- Ensure docker-entrypoint.sh is executable
- Check file ownership (should be www-data)

### Queue Not Processing
- Check queue worker: `docker-compose logs queue`
- Verify Redis connection: `docker-compose exec redis redis-cli ping`
- Check failed jobs: `php artisan queue:failed`

### Database Issues
- Verify PostgreSQL is running: `docker-compose logs postgres`
- Check migrations: `php artisan migrate:status`
- Reset database: `php artisan migrate:refresh`
