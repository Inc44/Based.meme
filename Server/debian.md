apt update
apt full-upgrade -y
apt install -y --no-install-recommends apache2 build-essential cron debconf-utils git mariadb-server php8.2 php8.2-apcu php8.2-bcmath php8.2-bz2 php8.2-cli php8.2-curl php8.2-gd php8.2-igbinary php8.2-imagick php8.2-intl php8.2-mbstring php8.2-mysql php8.2-opcache php8.2-pgsql php8.2-readline php8.2-redis php8.2-soap php8.2-tidy php8.2-xml php8.2-xmlrpc php8.2-zip
echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2" | debconf-set-selections
echo "phpmyadmin phpmyadmin/dbconfig-install boolean false" | debconf-set-selections
apt install -y --no-install-recommends phpmyadmin
sed -i 's/^ServerTokens OS/ServerTokens Prod/' /etc/apache2/conf-available/security.conf
sed -i 's/^ServerSignature On/ServerSignature Off/' /etc/apache2/conf-available/security.conf
rm -rf /var/www/html
git clone https://github.com/Inc44/Based.meme.git /var/www/html
apt clean
rm -rf /var/lib/apt/lists/*

vim /etc/environment

systemctl reboot

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

vim /etc/ssl/certs/cloudflare-origin.pem
vim /etc/ssl/private/cloudflare-origin-key.pem

a2enmod ssl
a2enmod rewrite

vim /etc/apache2/sites-available/basedmeme.info.conf

systemctl restart apache2

a2ensite basedmeme.info.conf

apache2ctl configtest