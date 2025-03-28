FROM alpine:3.18

ARG ADMIN_PASSWORD=password123
ENV ADMIN_PASSWORD=${ADMIN_PASSWORD}

COPY start.sh /start.sh

RUN apk update && \
	apk upgrade && \
	apk add --no-cache \
	apache2 \
	apache2-utils \
	build-base \
	cronie \
	dos2unix \
	git \
	mariadb \
	mariadb-client \
	mariadb-server-utils \
	php82 \
	php82-apache2 \
	php82-bcmath \
	php82-bz2 \
	php82-cli \
	php82-common \
	php82-curl \
	php82-gd \
	php82-intl \
	php82-json \
	php82-mbstring \
	php82-mysqli \
	php82-mysqlnd \
	php82-opcache \
	php82-openssl \
	php82-pdo \
	php82-pdo_mysql \
	php82-pecl-apcu \
	php82-pecl-imagick \
	php82-pecl-redis \
	php82-pgsql \
	php82-session \
	php82-soap \
	php82-tidy \
	php82-xml \
	php82-xmlreader \
	php82-zip \
	phpmyadmin && \
	echo 'ServerTokens Prod' > /etc/apache2/conf.d/security.conf && \
	echo 'ServerSignature Off' >> /etc/apache2/conf.d/security.conf && \
	mkdir -p /usr/share/webapps/phpmyadmin/tmp && \
	chmod 777 /usr/share/webapps/phpmyadmin/tmp && \
	rm -rf /var/www/localhost/htdocs && \
	git clone https://github.com/Inc44/Based.meme.git /var/www/localhost/htdocs && \
	dos2unix /start.sh && \
	chmod +x /start.sh && \
	rm -rf /var/cache/apk/*

EXPOSE 80 443

ENTRYPOINT ["/start.sh"]