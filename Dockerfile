FROM php:5.6-apache
EXPOSE 80

RUN apt-get update && apt-get install -y zlib1g-dev libicu-dev libssl-dev libpcre++-dev libmagickwand-dev node npm libpq-dev default-jre libmemcached-dev

RUN ln -s /usr/bin/nodejs /usr/local/bin/node

RUN docker-php-ext-install intl zip pdo_pgsql 
RUN pecl install mailparse-2.1.6 && pecl install mongo && pecl install oauth-1.2.3 && pecl install xdebug && pecl install imagick && pecl install memcached
RUN docker-php-ext-enable mailparse mongo oauth xdebug intl imagick memcached

RUN npm -g install less

RUN a2enmod authn_file.load && a2enmod authn_dbm.load && a2enmod \ 
	authn_anon.load && a2enmod authn_dbd.load && a2enmod authz_host.load \
	&& a2enmod authz_groupfile.load && \
	a2enmod authz_dbm.load && a2enmod authz_owner.load && a2enmod \
	auth_basic.load && a2enmod auth_digest.load && a2enmod cache.load \
	&& a2enmod dbd.load && a2enmod dump_io.load && a2enmod \
	reqtimeout.load && a2enmod ext_filter.load && a2enmod include.load && \
	a2enmod filter.load && a2enmod substitute.load && a2enmod deflate.load \
	&& a2enmod log_forensic.load && a2enmod env.load && a2enmod \
	expires.load && a2enmod headers.load && a2enmod usertrack.load  && \
	a2enmod setenvif.load && a2enmod proxy.load && \
	a2enmod rewrite.load && a2enmod proxy_ftp.load && a2enmod \
	proxy_http.load && a2enmod proxy_scgi.load && a2enmod proxy_ajp.load \
	&& a2enmod proxy_balancer.load && a2enmod ssl.load && a2enmod \
	mime.load && a2enmod dav.load && a2enmod status.load && a2enmod \
	autoindex.load && a2enmod asis.load && a2enmod info.load  && \
	a2enmod cgi.load && a2enmod dav_fs.load && a2enmod vhost_alias.load \
	&& a2enmod negotiation.load && a2enmod dir.load && a2enmod \
	actions.load && a2enmod speling.load && a2enmod userdir.load && \
	a2enmod alias.load

RUN mkdir /workspace
WORKDIR /workspace
RUN curl -sS https://getcomposer.org/installer | php -- -- install-dir=.

ADD . /code
WORKDIR /code
COPY apache-litus.conf /etc/apache2/sites-available/000-default.conf
RUN /workspace/composer.phar install
COPY php.ini-production /usr/local/etc/php/php.ini
