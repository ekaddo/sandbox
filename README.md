# Contact Manager Web App

A simple contact management system built with Nginx, PHP, and PostgreSQL running in Docker containers.

## Technologies Used

- **Nginx** - Web server (Alpine Linux)
- **PHP 8.2-FPM** - Backend processing (Alpine Linux)
- **PostgreSQL 15** - Database (Alpine Linux)
- **HTML/CSS/JavaScript** - Frontend

## Why PostgreSQL?

PostgreSQL was chosen over SQL Server for this Docker-based web app because:
- **Lightweight**: ~50MB image vs SQL Server's ~1.5GB
- **Better Docker support**: Official images with simpler configuration
- **Cross-platform**: Works seamlessly on Windows, Mac, and Linux
- **Web-friendly**: Excellent for CRUD operations and widely used in web development
- **Open source**: No licensing concerns

## Project Structure

```
sandbox/
├── docker-compose.yml      # Docker orchestration
├── nginx/
│   └── nginx.conf          # Nginx configuration
├── www/
│   ├── index.html          # Frontend UI
│   └── api.php             # Backend API
└── init-db/
    └── 01-init.sql         # Database initialization
```

## Features

- ✅ Create new contacts
- ✅ View all contacts in a table
- ✅ Update existing contacts
- ✅ Delete contacts
- ✅ Email validation
- ✅ Responsive design
- ✅ PostgreSQL database with persistent storage

## Quick Start (Deploying on New Machine)

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed deployment instructions on multiple machines.

**TL;DR:**
```bash
git clone https://github.com/YOUR_USERNAME/sandbox.git
cd sandbox
docker-compose up -d --build
# Access: http://localhost:8888
```

## Prerequisites

- Docker Desktop installed and running
- Port 8888 available (web interface)
- Port 5432 available (PostgreSQL)

## Setup Instructions

1. **Navigate to the project directory:**
   ```bash
   cd i:\dev\gravity\sandbox
   ```

2. **Start the Docker containers:**
   ```bash
   docker-compose up -d
   ```

3. **Wait for containers to start** (about 30 seconds for first run)

4. **Access the application:**
   - Open your browser to: http://localhost:8080
   - You should see the Contact Manager interface with 3 sample contacts

## Docker Commands

### Start the application:
```bash
docker-compose up -d
```

### Stop the application:
```bash
docker-compose down
```

### Stop and remove all data (including database):
```bash
docker-compose down -v
```

### View logs:
```bash
docker-compose logs

# Or for specific service:
docker-compose logs nginx
docker-compose logs php
docker-compose logs postgres
```

### Restart services:
```bash
docker-compose restart
```

### Check running containers:
```bash
docker-compose ps
```

## Editing Files

All files in the `www/` directory are mounted to the Docker containers:
- Edit `www/index.html` to modify the frontend
- Edit `www/api.php` to modify the backend API
- Changes are reflected immediately (no rebuild needed)

To modify the database schema:
- Edit `init-db/01-init.sql`
- Run `docker-compose down -v` to remove old data
- Run `docker-compose up -d` to recreate with new schema

## Database Access

### Connect to PostgreSQL from host machine:
```bash
# Using psql (if installed)
psql -h localhost -p 5432 -U webappuser -d contactsdb
# Password: webapppass
```

### Connect to PostgreSQL container:
```bash
docker exec -it postgres-db psql -U webappuser -d contactsdb
```

### Example SQL queries:
```sql
-- View all contacts
SELECT * FROM contacts;

-- Add a contact manually
INSERT INTO contacts (first_name, last_name, email, phone)
VALUES ('Alice', 'Williams', 'alice@example.com', '555-0104');

-- Update a contact
UPDATE contacts SET phone = '555-9999' WHERE id = 1;

-- Delete a contact
DELETE FROM contacts WHERE id = 1;
```

## Database Schema

```sql
contacts (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
```

## Troubleshooting

### Application not loading:
1. Check if containers are running: `docker-compose ps`
2. Check logs: `docker-compose logs`
3. Ensure ports 8080 and 5432 are not in use

### Database connection errors:
1. Wait 30 seconds for PostgreSQL to initialize
2. Check logs: `docker-compose logs postgres`
3. Restart containers: `docker-compose restart`

### Changes not appearing:
1. Hard refresh browser (Ctrl+F5)
2. Check file permissions
3. Check Nginx logs: `docker-compose logs nginx`

### PHP errors:
1. Check PHP logs: `docker-compose logs php`
2. Ensure PostgreSQL is running: `docker-compose ps postgres`

## Development Tips

### Installing PHP extensions (if needed):
The PHP container uses Alpine Linux. To add PostgreSQL PDO support (already included in php:8.2-fpm-alpine):
```dockerfile
# If you need additional PHP extensions, modify docker-compose.yml:
php:
  image: php:8.2-fpm-alpine
  command: sh -c "docker-php-ext-install pdo_pgsql && php-fpm"
```

### Hot reload:
- HTML/CSS/JS changes are instant
- PHP changes are instant
- Nginx config changes require: `docker-compose restart nginx`
- Database schema changes require: `docker-compose down -v && docker-compose up -d`

## Production Considerations

This is a development setup. For production:
- Change database credentials in `docker-compose.yml`
- Enable HTTPS with SSL certificates
- Add input sanitization and CSRF protection
- Implement authentication/authorization
- Use environment variables for sensitive data
- Add rate limiting
- Enable PHP error logging (not display)

## License

This is a learning project - feel free to use and modify as needed.
