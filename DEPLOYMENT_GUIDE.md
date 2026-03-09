# Deployment & Configuration Guide

## System Requirements

### Minimum
- PHP 7.4 or higher
- MySQL 5.7 or MariaDB 10.3
- 2GB RAM
- 50GB Disk Space

### Recommended
- PHP 8.1+
- MySQL 8.0+ or MariaDB 10.6+
- 8GB+ RAM
- 500GB+ Disk Space
- SSD Storage

### Required PHP Extensions
```bash
# Ubuntu/Debian
sudo apt-get install php8.1 php8.1-mysql php8.1-mbstring php8.1-xml php8.1-json php8.1-curl php8.1-gd php8.1-opcache

# CentOS/RHEL
sudo yum install php81 php81-mysqlnd php81-mbstring php81-xml php81-json php81-curl php81-gd php81-opcache
```

### Verify Installation
```bash
php -v
php -m | grep -E "mysql|mbstring|xml|json|curl|gd|opcache"
```

---

## Pre-Deployment Checklist

- [ ] Backup existing database
- [ ] Test on staging environment first
- [ ] Review security settings
- [ ] Prepare rollback plan
- [ ] Schedule maintenance window
- [ ] Notify users
- [ ] Verify all dependencies
- [ ] Test database migrations
- [ ] Review log files
- [ ] Setup monitoring

---

## Environment Setup

### 1. Create Project Directories

```bash
# Navigate to web root
cd /var/www/html

# Create application directory
mkdir scifi-conquest
cd scifi-conquest

# Create necessary subdirectories
mkdir -p logs cache sessions database backup
chmod 755 logs cache sessions database backup
chmod 777 cache sessions
```

### 2. Configure Permissions

```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/html/scifi-conquest

# Set permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Make writable directories
chmod 777 cache logs sessions database backup
```

### 3. Create .env File

```bash
# Copy environment template
cp .env.example .env

# Edit configuration
nano .env
```

**Production .env:**
```
# Database
DB_HOST=localhost
DB_USER=scifi_game
DB_PASSWORD=STRONG_PASSWORD_HERE
DB_NAME=scifi_conquest
DB_PORT=3306

# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://scifi-conquest.game

# Logging
LOG_LEVEL=error
LOG_PATH=/var/www/html/scifi-conquest/logs

# Cache
CACHE_ENABLED=true
CACHE_DRIVER=file
CACHE_TTL=3600

# Session
SESSION_TIMEOUT=3600
SESSION_PATH=/var/www/html/scifi-conquest/sessions

# Security
JWT_SECRET=YOUR_LONG_SECRET_KEY_HERE
SECURE_COOKIES=true
HTTPS_ONLY=true

# SMTP (Email)
SMTP_HOST=mail.example.com
SMTP_PORT=587
SMTP_USER=noreply@example.com
SMTP_PASS=smtp_password
SMTP_FROM=noreply@scifi-conquest.game

# Timezone
TIMEZONE=UTC

# Debug (disable in production)
DEBUG_MODE=false
```

### 4. Create System User

```bash
# Create non-root user for database
sudo mysql -u root -p

# Execute in MySQL:
mysql> CREATE USER 'scifi_game'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD';
mysql> GRANT ALL PRIVILEGES ON scifi_conquest.* TO 'scifi_game'@'localhost';
mysql> FLUSH PRIVILEGES;
mysql> EXIT;
```

---

## Database Setup

### 1. Create Database

```bash
# Create the database
sudo mysql -u root -p < Db/Dbgame.sql

# Verify creation
sudo mysql -u root -p scifi_conquest -e "SHOW TABLES;"
```

### 2. Initialize Database

```bash
# Run migrations if any
php Index/scripts/migrate.php

# Seed initial data
php Index/scripts/seed.php

# Verify data
sudo mysql -u root -p scifi_conquest -e "SELECT COUNT(*) FROM players;"
```

### 3. Setup Backups

```bash
# Create backup script
cat > /usr/local/bin/backup-scifi.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/var/backups/scifi-conquest"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="scifi_conquest"
DB_USER="scifi_game"
DB_PASS="STRONG_PASSWORD"

mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Cleanup old backups (keep last 30 days)
find $BACKUP_DIR -name "db_*.sql.gz" -mtime +30 -delete

echo "Backup completed: $BACKUP_DIR/db_$DATE.sql.gz"
EOF

# Make executable
chmod +x /usr/local/bin/backup-scifi.sh

# Add to crontab (daily at 2 AM)
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-scifi.sh
```

---

## Web Server Configuration

### Apache Configuration

```apache
# /etc/apache2/sites-available/scifi-conquest.conf

<VirtualHost *:80>
    ServerName scifi-conquest.game
    ServerAlias www.scifi-conquest.game
    
    # Redirect HTTP to HTTPS
    Redirect permanent / https://scifi-conquest.game/
</VirtualHost>

<VirtualHost *:443>
    ServerName scifi-conquest.game
    ServerAlias www.scifi-conquest.game
    
    DocumentRoot /var/www/html/scifi-conquest
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/scifi-conquest.game/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/scifi-conquest.game/privkey.pem
    SSLCertificateChainFile /etc/letsencrypt/live/scifi-conquest.game/chain.pem
    
    # Security Headers
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    
    # Logging
    ErrorLog ${APACHE_LOG_DIR}/scifi-conquest_error.log
    CustomLog ${APACHE_LOG_DIR}/scifi-conquest_access.log combined
    
    # PHP Handler
    <FilesMatch "\.php$">
        SetHandler "proxy:unix:/run/php/php8.1-fpm.sock|fcgi://localhost"
    </FilesMatch>
    
    # Deny access to sensitive files
    <FilesMatch "\.env|\.git|\.gitignore|composer\.lock">
        Order allow,deny
        Deny from all
    </FilesMatch>
    
    # Rewrite rules
    <Directory /var/www/html/scifi-conquest>
        Options -Indexes
        AllowOverride All
        Require all granted
        
        # API routing
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^api/(.*)$ Index/api/router.php?path=$1 [QSA,L]
        
        # Game routing
        RewriteRule ^(.*)$ index.php [QSA,L]
    </Directory>
</VirtualHost>
```

### Enable Configuration

```bash
# Enable the site
sudo a2ensite scifi-conquest.conf

# Enable required modules
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod ssl
sudo a2enmod proxy
sudo a2enmod proxy_fcgi

# Test configuration
sudo apache2ctl -t

# Restart Apache
sudo systemctl restart apache2
```

### Nginx Configuration

```nginx
# /etc/nginx/sites-available/scifi-conquest

upstream php_backend {
    server unix:/run/php/php8.1-fpm.sock;
}

server {
    listen 80;
    server_name scifi-conquest.game www.scifi-conquest.game;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name scifi-conquest.game www.scifi-conquest.game;
    
    root /var/www/html/scifi-conquest;
    index index.php;
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/scifi-conquest.game/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/scifi-conquest.game/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    
    # Security Headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    # Logging
    access_log /var/log/nginx/scifi-conquest_access.log combined;
    error_log /var/log/nginx/scifi-conquest_error.log warn;
    
    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }
    
    location ~ \.env$ {
        deny all;
    }
    
    # PHP routing
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass php_backend;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # API routing
    location /api {
        try_files $uri $uri/ /Index/api/router.php?path=$uri&$args;
    }
    
    # Static files
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Main rewrite
    location / {
        try_files $uri $uri/ /index.php?$args;
    }
}
```

### Enable Nginx Configuration

```bash
# Enable the site
sudo ln -s /etc/nginx/sites-available/scifi-conquest /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

---

## SSL/TLS Setup

### Using Let's Encrypt

```bash
# Install Certbot
sudo apt-get install certbot python3-certbot-apache
# or for Nginx
sudo apt-get install certbot python3-certbot-nginx

# Generate certificate
sudo certbot certonly --webroot -w /var/www/html/scifi-conquest -d scifi-conquest.game -d www.scifi-conquest.game

# Auto-renewal
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer

# Verify renewal
sudo certbot renew --dry-run
```

---

## Performance Optimization

### PHP Configuration

```ini
# /etc/php/8.1/fpm/php.ini

; Memory
memory_limit = 512M

; Execution time
max_execution_time = 300

; Upload
upload_max_filesize = 100M
post_max_size = 100M

; Opcache (very important for production)
opcache.enable = 1
opcache.memory_consumption = 256
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 300
opcache.fast_shutdown = 1

; Sessions
session.gc_maxlifetime = 3600
session.gc_probability = 1
session.gc_divisor = 100

; Timezone
date.timezone = UTC

; Display errors (disable in production)
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log
```

### MySQL Performance

```sql
-- Optimize table queries
-- Add these to my.cnf [mysqld] section
-- innodb_buffer_pool_size = 2G
-- innodb_log_file_size = 512M
-- max_connections = 300
-- slow_query_log = ON
-- long_query_time = 2

-- Check table status
SELECT * FROM information_schema.INNODB_TRX;

-- Optimize tables
OPTIMIZE TABLE players;
OPTIMIZE TABLE planets;
OPTIMIZE TABLE fleets;

-- Analyze tables
ANALYZE TABLE game_statistics;
```

### Caching Strategy

```php
<?php
// Enable query result caching
$cache = cache();

// Cache player data for 5 minutes
$playerId = 123;
$cacheKey = "player_{$playerId}";

if (!$player = $cache->get($cacheKey)) {
    $player = db()->fetchOne("SELECT * FROM players WHERE id = ?", [$playerId]);
    $cache->set($cacheKey, $player, 300);
}
```

---

## Security Hardening

### File Permissions

```bash
# Read-only for sensitive files
chmod 400 .env
chmod 400 Db/Config.php

# Restricted web access
chmod 755 Index/classes
chmod 755 Index/api
chmod 755 Index/pages

# Cache/Logs writable
chmod 777 cache logs sessions
```

### Firewall Configuration

```bash
# UFW (Ubuntu)
sudo ufw allow 22/tcp       # SSH
sudo ufw allow 80/tcp       # HTTP
sudo ufw allow 443/tcp      # HTTPS
sudo ufw allow 3306/tcp     # MySQL (internal only)
sudo ufw enable
```

### Database Security

```bash
# Remove test database
sudo mysql -u root -p
mysql> DROP DATABASE test;

# Remove anonymous users
mysql> DELETE FROM mysql.user WHERE User='';

# Remove remote root access
mysql> UPDATE mysql.user SET Host='localhost' WHERE User='root';

# Flush privileges
mysql> FLUSH PRIVILEGES;
```

### Application Security

1. **Update Dependencies**
```bash
composer update
```

2. **Disable Directory Listing**
```bash
# Add to .htaccess
Options -Indexes
```

3. **Secure Headers** (already configured in web server)

4. **Input Validation**
- Use Validator class
- Escape all output
- Use parameterized queries

---

## Monitoring & Logging

### Log Rotation

```bash
# /etc/logrotate.d/scifi-conquest

/var/www/html/scifi-conquest/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
    postrotate
        systemctl reload php8.1-fpm > /dev/null 2>&1 || true
    endscript
}
```

### System Monitoring

```bash
# Install monitoring tools
sudo apt-get install htop iostat iotop

# Monitor resources
htop
iostat -x 1

# Check disk usage
df -h
du -h /var/www/html/scifi-conquest

# Check logs
tail -f logs/error.log
tail -f logs/game.log
```

---

## Backup & Recovery

### Automated Backup Strategy

```bash
# Daily database backup
0 2 * * * /usr/local/bin/backup-scifi.sh

# Weekly file backup
0 3 * * 0 tar -czf /var/backups/scifi-files_$(date +\%Y\%m\%d).tar.gz /var/www/html/scifi-conquest

# Monthly archive
0 4 1 * * /usr/local/bin/archive-backups.sh
```

### Recovery Procedure

```bash
# Stop services
sudo systemctl stop apache2 nginx
sudo systemctl stop mysql

# Restore database
sudo mysql -u root -p scifi_conquest < /var/backups/db_backup.sql

# Restore files (if needed)
sudo tar -xzf /var/backups/scifi-files_backup.tar.gz -C /var/www/html/

# Start services
sudo systemctl start mysql
sudo systemctl start apache2
```

---

## Deployment Checklist

### Pre-Deployment
- [ ] Database backed up
- [ ] Application tested
- [ ] Configuration reviewed
- [ ] SSL certificates ready

### Deployment
- [ ] Upload code
- [ ] Set permissions
- [ ] Configure .env
- [ ] Run migrations
- [ ] Clear caches
- [ ] Start services

### Post-Deployment
- [ ] Verify application running
- [ ] Check error logs
- [ ] Test key features
- [ ] Monitor performance
- [ ] Document changes

---

## Troubleshooting

### Application Not Loading
```bash
# Check PHP-FPM
sudo systemctl status php8.1-fpm

# Check web server
sudo systemctl status apache2  # or nginx

# Check file permissions
ls -la /var/www/html/scifi-conquest/.env

# Check error logs
tail -f /var/log/php/error.log
```

### Database Connection Failed
```bash
# Check MySQL
sudo systemctl status mysql

# Test connection
mysql -u scifi_game -p -h localhost scifi_conquest

# Check permissions
SELECT User, Host FROM mysql.user;
```

### Performance Issues
```bash
# Check slow queries
mysql> SHOW PROCESSLIST;

# Monitor system
top
# or
vmstat 1

# Check cache
php -r "echo json_encode((new Cache())->getStats());"
```

---

**Last Updated:** 2024
**Version:** 1.0
