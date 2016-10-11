FROM phusion/baseimage:0.9.15

MAINTAINER Peter Corcoran <peter.corcoran@shadowacre.ltd>

EXPOSE 80

ENV LANG        en_US.UTF-8
ENV LC_ALL      en_US.UTF-8
ENV HOME        /root
ENV tiller_json '{}'

CMD ["/usr/local/bin/tiller" , "-v"]

RUN locale-gen en_US.UTF-8
RUN /etc/my_init.d/00_regen_ssh_host_keys.sh

RUN DEBIAN_FRONTEND="noninteractive" apt-get update && apt-get install -y curl \
	git build-essential python-software-properties

RUN add-apt-repository -y ppa:ondrej/php
RUN add-apt-repository -y ppa:brightbox/ruby-ng
RUN add-apt-repository -y ppa:nginx/stable

RUN DEBIAN_FRONTEND="noninteractive" apt-get update && apt-get install -y \
	php5.6 php5.6-cli php5.6-fpm php5.6-mysql php5.6-sqlite php5.6-curl \
	php5.6-gd php5.6-mcrypt php5.6-intl php5.6-imap php5.6-tidy php5.6-xml \
	php5.6-zip nginx ruby1.9.3

RUN gem1.9.3 install tiller

RUN curl -sS https://getcomposer.org/installer | php -- \
	--install-dir=/usr/local/bin --filename=composer

RUN echo "daemon off;" >> /etc/nginx/nginx.conf; \
	sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /etc/php/5.6/fpm/php-fpm.conf; \
	sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/5.6/fpm/php.ini; \
	sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php/5.6/fpm/php.ini; \
	sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php/5.6/cli/php.ini

RUN rm -rf /var/www; \
 	mkdir -p /var/www; \
	mkdir -p /var/www/cache; \
	chown -R www-data.www-data /var/www

ADD ./composer.* /var/www/

RUN echo invalidating cache 0

RUN cd /var/www; \
	sudo -u www-data composer install --no-interaction; \
	cp -r /var/www/vendor/ouropencode/dachi/docker /etc/tiller

ADD ./ /var/www

RUN chown -R www-data.www-data /var/www

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
