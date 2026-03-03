#!/bin/bash
set -e

echo "🚀 Starting Lab SQLi..."

# Init database jika belum ada
if [ ! -d "/var/lib/mysql/labsqli" ]; then
    echo "📦 Initializing database..."

    # Init MySQL data directory
    mysql_install_db --user=mysql --datadir=/var/lib/mysql > /dev/null 2>&1

    # Start MySQL sementara
    mysqld_safe --skip-networking --user=mysql &
    MYSQL_PID=$!

    # Tunggu siap
    for i in $(seq 1 30); do
        if mysqladmin ping --silent 2>/dev/null; then
            echo "   MySQL ready!"
            break
        fi
        echo "   Waiting MySQL... ($i/30)"
        sleep 1
    done

    # Setup user & database
    mysql -u root <<-EOSQL
        CREATE DATABASE IF NOT EXISTS labsqli CHARACTER SET utf8mb4;
        CREATE USER IF NOT EXISTS 'labuser'@'localhost' IDENTIFIED BY 'labpass123';
        GRANT ALL PRIVILEGES ON labsqli.* TO 'labuser'@'localhost';
        FLUSH PRIVILEGES;
EOSQL

    # Import data
    mysql -u root labsqli < /docker-entrypoint-initdb.sql
    echo "✅ Database ready!"

    # Stop MySQL sementara
    mysqladmin -u root shutdown 2>/dev/null || kill $MYSQL_PID 2>/dev/null
    wait $MYSQL_PID 2>/dev/null || true
    sleep 2
fi

echo "🎯 Starting Apache + MySQL via supervisor..."
mkdir -p /var/log/supervisor
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
