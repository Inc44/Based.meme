#!/bin/bash
set -e

service mariadb start

mysql --user=root --password="${ADMIN_PASSWORD}" <<-EOSQL
	GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY '${ADMIN_PASSWORD}' WITH GRANT OPTION;
	CREATE DATABASE IF NOT EXISTS phpmyadmin;
	USE phpmyadmin;
	SOURCE /usr/share/phpmyadmin/sql/create_tables.sql;
EOSQL

echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2" | debconf-set-selections
echo "phpmyadmin phpmyadmin/dbconfig-install boolean true" | debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/app-pass password ${ADMIN_PASSWORD}" | debconf-set-selections
echo "phpmyadmin phpmyadmin/app-password-confirm password ${ADMIN_PASSWORD}" | debconf-set-selections

dpkg-reconfigure -f noninteractive phpmyadmin

service cron start

(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/html && git pull origin master") | crontab -

apache2ctl -D FOREGROUND