# Deployment Guide

This guide explains how to deploy the Contact Manager app on any machine with Docker Desktop.

## Prerequisites

- Docker Desktop installed and running
- Git installed
- Ports 8888 and 5432 available

## Quick Start (New Machine)

### 1. Clone the Repository

```bash
# Clone from GitHub
git clone https://github.com/YOUR_USERNAME/sandbox.git
cd sandbox
```

### 2. Start the Application

```bash
# Build and start all containers
docker-compose up -d --build

# Wait 30 seconds for PostgreSQL to initialize
# Then access: http://localhost:8888
```

### 3. Verify It's Running

```bash
# Check container status
docker-compose ps

# View logs if needed
docker-compose logs
```

That's it! The app should be running at http://localhost:8888

## What Gets Created

When you run `docker-compose up`:

1. **Docker volumes**: `postgres-data` (stores database persistently)
2. **Docker network**: `sandbox_webapp-network` (connects containers)
3. **Containers**: nginx-webserver, php-backend, postgres-db

## Port Conflicts

If port 8888 or 5432 is already in use:

1. Edit `docker-compose.yml`
2. Change the port mapping:
   ```yaml
   # For Nginx (change 8888 to something else)
   ports:
     - "9999:80"  # Use port 9999 instead

   # For PostgreSQL (change 5432 to something else)
   ports:
     - "5433:5432"  # Use port 5433 instead
   ```
3. Restart: `docker-compose down && docker-compose up -d`

## Making Changes

### Edit Code (Hot Reload)
- **Frontend**: Edit `www/index.html` - changes are instant
- **Backend**: Edit `www/api.php` - changes are instant
- Just refresh your browser!

### Edit Nginx Config
```bash
# Edit nginx/nginx.conf
# Then restart:
docker-compose restart nginx
```

### Edit Database Schema
```bash
# Edit init-db/01-init.sql
# Then recreate database:
docker-compose down -v
docker-compose up -d
```

## Stopping the Application

```bash
# Stop containers (keeps data)
docker-compose down

# Stop and DELETE all data
docker-compose down -v
```

## Data Persistence

### Where is data stored?

- **Source code**: Your local filesystem (tracked by Git)
- **Database data**: Docker volume `postgres-data` (NOT in Git)
- **Configuration**: Local files (tracked by Git)

### Important Notes:

1. **Database data is LOCAL to each machine**
   - Each deployment starts with sample data from `init-db/01-init.sql`
   - Changes to contacts are NOT synced between machines
   - To reset data: `docker-compose down -v && docker-compose up -d`

2. **Code changes ARE synced via Git**
   - Push changes: `git push`
   - Pull on other machine: `git pull`
   - Restart if needed: `docker-compose restart`

## Multi-Machine Workflow

### On Development Machine (Home Laptop)
```bash
# Make changes to code
vim www/api.php

# Test locally
# http://localhost:8888

# Commit and push
git add .
git commit -m "Updated API logic"
git push origin master
```

### On Work Machine
```bash
# Pull latest changes
git pull origin master

# Containers auto-reload for PHP/HTML
# Just refresh browser!

# If you changed docker-compose.yml or Dockerfile.php:
docker-compose up -d --build
```

## Troubleshooting

### "Port already in use"
- Change ports in `docker-compose.yml` (see Port Conflicts section)

### "Database connection failed"
- Wait 30 seconds for PostgreSQL to start
- Check: `docker-compose logs postgres`

### "Changes not appearing"
- Hard refresh: Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)
- For Nginx config: `docker-compose restart nginx`

### "Containers won't start"
- Check Docker Desktop is running
- Check logs: `docker-compose logs`
- Reset everything: `docker-compose down -v && docker-compose up -d --build`

## Production Deployment

This is a **development setup**. For production:

1. Use environment variables for secrets
2. Enable HTTPS with SSL certificates
3. Remove port 5432 exposure (only internal)
4. Add authentication
5. Use a reverse proxy (Traefik, Caddy)
6. Consider using Docker Swarm or Kubernetes

## Architecture

```
Your Machine
├── sandbox/ (Git repo)
│   ├── docker-compose.yml
│   ├── Dockerfile.php
│   ├── nginx/nginx.conf
│   ├── www/index.html
│   ├── www/api.php
│   └── init-db/01-init.sql
│
Docker Desktop
├── Containers (ephemeral - recreated on restart)
│   ├── nginx-webserver
│   ├── php-backend
│   └── postgres-db
│
└── Volumes (persistent - survives restarts)
    └── postgres-data (database files)
```

## Tips

1. **Use the same Docker version** across machines for consistency
2. **Don't commit sensitive data** (passwords, API keys) - use environment variables
3. **Keep docker-compose.yml simple** - complexity makes it harder to debug
4. **Document custom changes** in this file
5. **Test on a fresh clone** before pushing to ensure it works

## Next Steps

- Add authentication
- Implement search/filtering
- Add pagination for large contact lists
- Set up CI/CD pipeline
- Deploy to cloud (AWS, DigitalOcean, etc.)
