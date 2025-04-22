FROM debian:bookworm-slim

ARG ADMIN_PASSWORD=password123
ENV ADMIN_PASSWORD=${ADMIN_PASSWORD}

COPY start.sh /start.sh

RUN apt update && \
	apt full-upgrade -y && \
	apt install -y --no-install-recommends \
	apache2 \
	build-essential \
	cron \
	debconf-utils \
	git \
	mariadb-server \
	php8.2 \
	php8.2-apcu \
	php8.2-bcmath \
	php8.2-bz2 \
	php8.2-cli \
	php8.2-curl \
	php8.2-gd \
	php8.2-igbinary \
	php8.2-imagick \
	php8.2-intl \
	php8.2-mbstring \
	php8.2-mysql \
	php8.2-opcache \
	php8.2-pgsql \
	php8.2-readline \
	php8.2-redis \
	php8.2-soap \
	php8.2-tidy \
	php8.2-xml \
	php8.2-xmlrpc \
	php8.2-zip && \
	echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2" | debconf-set-selections && \
	echo "phpmyadmin phpmyadmin/dbconfig-install boolean false" | debconf-set-selections && \
	apt install -y --no-install-recommends phpmyadmin && \
	sed -i 's/^ServerTokens OS/ServerTokens Prod/' /etc/apache2/conf-available/security.conf && \
	sed -i 's/^ServerSignature On/ServerSignature Off/' /etc/apache2/conf-available/security.conf && \
	rm -rf /var/www/html && \
	git clone https://github.com/Inc44/Based.meme.git /var/www/html && \
	chmod +x /start.sh && \
	apt clean && \
	rm -rf /var/lib/apt/lists/*

EXPOSE 80 443

ENTRYPOINT ["/start.sh"]