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
RUN add-apt-repository -y ppa:ondrej/php
RUN apt-add-repository -y ppa:brightbox/ruby-ng
RUN add-apt-repository -y ppa:nginx/stable

RUN DEBIAN_FRONTEND="noninteractive" apt-get update && apt-get install -y \
	curl build-essential python-software-properties php5-cli php5-fpm \
	php5-mysql php5-pgsql php5-sqlite php5-curl php5-gd php5-mcrypt php5-intl \
	php5-imap php5-tidy nginx ruby1.9.3

RUN gem1.9.3 install tiller

RUN curl -sS https://getcomposer.org/installer | php -- \
	--install-dir=/usr/local/bin --filename=composer

RUN echo "daemon off;" >> /etc/nginx/nginx.conf
RUN sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /etc/php5/fpm/php-fpm.conf
RUN sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php5/fpm/php.ini
RUN sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php5/fpm/php.ini
RUN sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php5/cli/php.ini

ADD ./composer.json /var/www/composer.json

RUN cd /var/www && composer install --no-interaction

ADD ./ /var/www

ADD docker /etc/tiller

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
