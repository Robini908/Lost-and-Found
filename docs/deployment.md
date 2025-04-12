# Lost & Found System - Deployment Instructions

## Table of Contents

- [System Requirements](#system-requirements)
- [Deployment Options](#deployment-options)
  - [Docker Deployment](#docker-deployment)
  - [Traditional Server Deployment](#traditional-server-deployment)
  - [Cloud Provider Deployment](#cloud-provider-deployment)
- [Environment Configuration](#environment-configuration)
- [Database Setup](#database-setup)
- [External Service Integration](#external-service-integration)
- [Security Considerations](#security-considerations)
- [Post-Deployment Verification](#post-deployment-verification)
- [Scaling Considerations](#scaling-considerations)
- [Troubleshooting](#troubleshooting)

## System Requirements

### Minimum Hardware Requirements

- **CPU:** 2+ cores
- **RAM:** 4GB+ (8GB+ recommended for production)
- **Storage:** 20GB+ SSD (40GB+ recommended for production)
- **Network:** 100Mbps+ with public IP address or domain

### Software Requirements

- **PHP:** 8.1+ with the following extensions:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML
  - GD (for image processing)
  - Redis (for caching and WebSocket)
  - Sodium (for cryptography)
- **Database:** MySQL 8.0+ or MariaDB 10.6+
- **Web Server:** Nginx 1.18+ or Apache 2.4+
- **Node.js:** 16.0+ (for frontend asset compilation)
- **Composer:** 2.0+
- **Redis:** 6.0+ (for caching, queues, and WebSocket)
- **SSL Certificate:** Required for production deployment

## Deployment Options

### Docker Deployment

Docker deployment is the recommended method for ensuring consistency across environments and simplified scaling.

#### Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+
- Git

#### Deployment Steps

1. **Clone the repository:**

```bash
git clone https://github.com/your-username/lost-found.git
cd lost-found
```

2. **Configure environment variables:**

```bash
cp .env.example .env
```

Edit the `.env` file to set appropriate values for your environment.

3. **Build and start containers:**

```bash
docker-compose up -d
```

4. **Install dependencies and initialize application:**

```bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan storage:link
docker-compose exec app npm install
docker-compose exec app npm run build
```

5. **Configure Nginx reverse proxy (optional but recommended):**

Create an Nginx configuration file on your host machine that forwards requests to the Docker containers.

```nginx
server {
    listen 80;
    server_name your-domain.com;
    
    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

6. **Set up SSL with Let's Encrypt:**

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### Traditional Server Deployment

For environments where Docker is not available or a more customized setup is required.

#### Prerequisites

- Access to a Linux server (Ubuntu 20.04+ recommended)
- SSH access with sudo privileges
- Domain name pointed to your server

#### Deployment Steps

1. **Update system packages:**

```bash
sudo apt update && sudo apt upgrade -y
```

2. **Install required packages:**

```bash
sudo apt install -y nginx mysql-server php8.1-fpm php8.1-cli php8.1-common php8.1-curl php8.1-mbstring php8.1-mysql php8.1-xml php8.1-zip php8.1-bcmath php8.1-gd php8.1-redis unzip git redis-server
```

3. **Configure MySQL:**

```bash
sudo mysql_secure_installation
```

Create database and user:

```sql
CREATE DATABASE lost_found CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'lost_found_user'@'localhost' IDENTIFIED BY 'your-secure-password';
GRANT ALL PRIVILEGES ON lost_found.* TO 'lost_found_user'@'localhost';
FLUSH PRIVILEGES;
```

4. **Install Composer:**

```bash
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

5. **Install Node.js:**

```bash
curl -fsSL https://deb.nodesource.com/setup_16.x | sudo -E bash -
sudo apt install -y nodejs
```

6. **Configure Nginx:**

Create a new Nginx server block:

```bash
sudo nano /etc/nginx/sites-available/lost-found
```

Add the following configuration:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/lost-found/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/lost-found /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

7. **Deploy application code:**

```bash
sudo mkdir -p /var/www
cd /var/www
sudo git clone https://github.com/your-username/lost-found.git
cd lost-found
sudo composer install --no-dev --optimize-autoloader
```

8. **Configure environment:**

```bash
sudo cp .env.example .env
sudo php artisan key:generate
```

Edit the `.env` file with appropriate settings:

```bash
sudo nano .env
```

9. **Set proper permissions:**

```bash
sudo chown -R www-data:www-data /var/www/lost-found
sudo chmod -R 755 /var/www/lost-found
sudo chmod -R 775 /var/www/lost-found/storage /var/www/lost-found/bootstrap/cache
```

10. **Run migrations and setup application:**

```bash
sudo -u www-data php artisan migrate --seed
sudo -u www-data php artisan storage:link
sudo -u www-data php artisan optimize
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

11. **Compile frontend assets:**

```bash
npm install
npm run build
```

12. **Set up SSL with Let's Encrypt:**

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

13. **Configure supervisor for queue workers:**

```bash
sudo apt install -y supervisor
```

Create configuration file:

```bash
sudo nano /etc/supervisor/conf.d/lost-found-worker.conf
```

Add configuration:

```ini
[program:lost-found-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/lost-found/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/lost-found/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

### Cloud Provider Deployment

#### AWS Deployment

1. **Launch EC2 Instance:**
   - Amazon Linux 2 or Ubuntu 20.04 LTS
   - t3.medium or higher (for production)
   - Enable public IP

2. **Configure Security Groups:**
   - Allow HTTP (80), HTTPS (443), and SSH (22)

3. **Configure RDS for MySQL:**
   - db.t3.small or higher (for production)
   - MySQL 8.0+
   - Enable Multi-AZ for production

4. **Set up Elasticache for Redis:**
   - cache.t3.micro or higher
   - Redis engine 6.x+

5. **Deploy using either Docker or Traditional methods** from the sections above.

6. **Set up an Application Load Balancer (ALB)** for SSL termination and traffic distribution if using multiple instances.

7. **Configure S3 for file storage:**
   - Create new bucket
   - Set appropriate CORS policy
   - Update `.env` to use S3 for file storage

#### Digital Ocean Deployment

1. **Create Droplet:**
   - Ubuntu 20.04 LTS
   - Basic Plan: Standard 2GB/2CPU or higher
   - Enable backups

2. **Create Managed Database:**
   - MySQL 8
   - 1GB or higher (depending on expected load)

3. **Create Managed Redis:**
   - Redis 6.x
   - 1GB plan

4. **Follow Traditional Server Deployment steps** above.

5. **Add a Load Balancer** if using multiple droplets.

## Environment Configuration

Key environment variables that must be properly configured:

```dotenv
# Application
APP_NAME="Lost & Found System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=lost_found
DB_USERNAME=lost_found_user
DB_PASSWORD=your-secure-password

# Queue
QUEUE_CONNECTION=redis

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="no-reply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Twilio (for SMS)
TWILIO_ACCOUNT_SID=your-account-sid
TWILIO_AUTH_TOKEN=your-auth-token
TWILIO_FROM_NUMBER=your-twilio-phone-number

# OpenAI (for matching algorithm)
OPENAI_API_KEY=your-openai-api-key
OPENAI_ORGANIZATION=your-openai-organization

# Storage
FILESYSTEM_DISK=local # or 's3' for production with large files
AWS_ACCESS_KEY_ID=your-aws-key
AWS_SECRET_ACCESS_KEY=your-aws-secret
AWS_DEFAULT_REGION=your-aws-region
AWS_BUCKET=your-bucket-name
AWS_USE_PATH_STYLE_ENDPOINT=false

# Google Maps
GOOGLE_MAPS_API_KEY=your-google-maps-api-key

# Contact Email
CONTACT_EMAIL=abrahamopuba@gmail.com
```

## Database Setup

### Database Migrations

Run migrations to set up database schema:

```bash
php artisan migrate
```

### Seeding Initial Data

Seed the database with required initial data:

```bash
php artisan db:seed
```

### Optimization for Production

```bash
# Add indexes for performance
php artisan db:optimize

# Run MySQL optimization
sudo mysqltuner --host localhost --user root --pass your-root-password
```

## External Service Integration

### Twilio Setup

1. Create a Twilio account at https://www.twilio.com
2. Purchase a phone number
3. Get your Account SID and Auth Token from the dashboard
4. Update `.env` file with these credentials

### OpenAI API Configuration

1. Create an account at https://platform.openai.com
2. Generate an API key
3. Update `.env` file with your API key and organization ID

### AWS S3 (for file storage)

1. Create an AWS account if you don't have one
2. Create a new S3 bucket
3. Create an IAM user with S3 access
4. Update `.env` file with your credentials

### Google Maps API

1. Create a project in Google Cloud Console
2. Enable Maps JavaScript API
3. Create an API key with HTTP referrer restrictions
4. Update `.env` file with your API key

## Security Considerations

### SSL Configuration

Ensure proper SSL configuration in Nginx:

```nginx
ssl_protocols TLSv1.2 TLSv1.3;
ssl_prefer_server_ciphers on;
ssl_ciphers "ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384";
ssl_session_cache shared:SSL:10m;
ssl_session_timeout 10m;
ssl_session_tickets off;
ssl_stapling on;
ssl_stapling_verify on;
add_header X-Frame-Options DENY;
add_header X-Content-Type-Options nosniff;
add_header X-XSS-Protection "1; mode=block";
add_header Content-Security-Policy "default-src 'self' https://api.twilio.com https://api.openai.com https://maps.googleapis.com; script-src 'self' 'unsafe-inline' https://maps.googleapis.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https://maps.googleapis.com; connect-src 'self' https://api.twilio.com https://api.openai.com https://maps.googleapis.com; frame-src 'none'; font-src 'self'; object-src 'none'; media-src 'self'; worker-src 'self'";
```

### File Permissions

Ensure proper file permissions:

```bash
sudo find /var/www/lost-found -type f -exec chmod 644 {} \;
sudo find /var/www/lost-found -type d -exec chmod 755 {} \;
sudo chown -R www-data:www-data /var/www/lost-found
sudo chmod -R 775 /var/www/lost-found/storage /var/www/lost-found/bootstrap/cache
```

### Database Security

1. Use a strong, unique password for database
2. Restrict database user permissions to only what's necessary
3. Enable MySQL/MariaDB security features

```sql
SET GLOBAL log_bin_trust_function_creators = 0;
SET GLOBAL automatic_sp_privileges = 0;
```

### Firewall Configuration

Set up UFW (Uncomplicated Firewall):

```bash
sudo ufw allow ssh
sudo ufw allow http
sudo ufw allow https
sudo ufw enable
```

## Post-Deployment Verification

### Application Health Check

```bash
# Check application status
php artisan status

# Verify queue operation
php artisan queue:monitor

# Test email sending
php artisan mail:test abrahamopuba@gmail.com

# Verify cache operation
php artisan cache:test
```

### Automated Testing

Run the test suite to verify functionality:

```bash
php artisan test
```

### Performance Testing

Run basic load testing:

```bash
# Install wrk
sudo apt install -y wrk

# Test homepage performance
wrk -t12 -c400 -d30s https://your-domain.com
```

## Scaling Considerations

### Horizontal Scaling

1. **Web Tier Scaling:**
   - Deploy multiple application servers
   - Use a load balancer (Nginx, HAProxy, or cloud provider LB)
   - Ensure session sharing via Redis

2. **Database Scaling:**
   - Master-slave replication for read scaling
   - Consider sharding for write-heavy workloads
   - Use connection pooling

3. **Caching Tier:**
   - Redis cluster for distributed caching
   - Implement cache tags for efficient invalidation

### Vertical Scaling

1. **Application Server:**
   - Increase PHP-FPM workers based on formula: `(Server RAM - Reserved RAM) / PHP Worker RAM Usage`
   - Optimize PHP opcache settings
   - Adjust Nginx worker connections

2. **Database Server:**
   - Tune InnoDB buffer pool size (typically 70-80% of available RAM)
   - Optimize query cache (if using MySQL <8.0)
   - Implement proper indexing strategy

3. **Redis Server:**
   - Enable persistence for reliability
   - Configure memory limits appropriately
   - Set proper eviction policies

## Troubleshooting

### Common Issues and Solutions

1. **Application Returns 500 Error:**
   - Check PHP error logs: `sudo tail -f /var/log/php8.1-fpm.log`
   - Check Laravel logs: `sudo tail -f /var/www/lost-found/storage/logs/laravel.log`
   - Verify file permissions and ownership

2. **Queue Workers Not Processing:**
   - Check supervisor logs: `sudo supervisorctl status`
   - Verify Redis connection
   - Check failed jobs table: `php artisan queue:failed`

3. **Slow Database Queries:**
   - Enable query logging temporarily: `SET global log_queries_not_using_indexes=ON;`
   - Analyze slow query log: `sudo tail -f /var/log/mysql/mysql-slow.log`
   - Run `EXPLAIN` on problematic queries

4. **Memory Issues:**
   - Check PHP-FPM memory limit in `php.ini`
   - Monitor system memory: `htop`
   - Consider implementing PHP OPcache

### Logging and Monitoring

1. **Log Management:**
   - Configure daily log rotation
   - Consider a centralized logging solution (ELK stack)

2. **System Monitoring:**
   - Set up server monitoring (Prometheus, Grafana)
   - Monitor key metrics: CPU, memory, disk I/O, network
   - Set up alerts for critical thresholds

3. **Application Performance Monitoring:**
   - Implement New Relic or similar APM solution
   - Set up custom Laravel Telescope monitoring in non-production environments

### Support Contacts

For technical issues with the deployment, please contact:

- Email: abrahamopuba@gmail.com
- System Administrator: Abraham Opuba
- Response Time: Within 48 hours

---

**Note:** This deployment guide assumes a Linux-based environment. Windows-based deployment would require different commands and configurations.

Document Version: 1.0  
Last Updated: April 12, 2025 
