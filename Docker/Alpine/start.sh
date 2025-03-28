#!/bin/sh
set -e

mkdir -p /run/mysqld
chown -R mysql:mysql /run/mysqld

if [ ! -d "/var/lib/mysql/mysql" ]; then
	mysql_install_db --user=mysql --datadir=/var/lib/mysql
fi

mysqld --user=mysql --datadir=/var/lib/mysql --skip-networking &

MYSQL_READY=0
for i in $(seq 30); do
	if mysqladmin ping -u root --silent; then
		MYSQL_READY=1
		break
	fi
	sleep 1
done

if [ "$MYSQL_READY" -eq 0 ]; then
	exit 1
fi

mysql --user=root <<EOSQL
	GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY '${ADMIN_PASSWORD}' WITH GRANT OPTION;
	CREATE DATABASE IF NOT EXISTS phpmyadmin;
	USE phpmyadmin;
	SOURCE /usr/share/webapps/phpmyadmin/sql/create_tables.sql;
EOSQL

crond

(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/localhost/htdocs && git pull origin master") | crontab -

httpd -D FOREGROUND