FROM phusion/baseimage:0.9.15

MAINTAINER Peter Corcoran <peter.corcoran@shadowacre.ltd>

CMD ["/usr/local/bin/tiller" , "-v"]

EXPOSE 80

ENV LANG          en_US.UTF-8
ENV LC_ALL        en_US.UTF-8
ENV NVM_DIR       /usr/local/nvm
ENV NODE_VERSION  5.12.0
ENV NODE_PATH     $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV HOME          /root
ENV PATH          $NVM_DIR/v$NODE_VERSION/bin:$PATH
ENV tiller_json   '{}'

SHELL ["/bin/bash", "-ci"]

# Get the system ready with some default stuff we'll need
RUN locale-gen en_US.UTF-8
RUN /etc/my_init.d/00_regen_ssh_host_keys.sh
RUN DEBIAN_FRONTEND="noninteractive" apt-get update && apt-get install -y curl \
	git build-essential python-software-properties

# Add our required PPA repositories for php, ruby and nginx
RUN add-apt-repository -y ppa:ondrej/php
RUN add-apt-repository -y ppa:brightbox/ruby-ng
RUN add-apt-repository -y ppa:nginx/stable

# Install php, ruby and nginx
RUN DEBIAN_FRONTEND="noninteractive" apt-get update && apt-get install -y \
	php5.6 php5.6-cli php5.6-fpm php5.6-mysql php5.6-sqlite php5.6-curl \
	php5.6-gd php5.6-mcrypt php5.6-intl php5.6-imap php5.6-tidy php5.6-xml \
	php5.6-zip nginx ruby1.9.3

# Install tiller for managing docker configuration templates
RUN gem1.9.3 install tiller

# Install composer for managing php packages
RUN curl -sS https://getcomposer.org/installer | php -- \
	--install-dir=/usr/local/bin --filename=composer

# Install node for compiling assets with webpack
RUN curl -sS https://raw.githubusercontent.com/creationix/nvm/v0.32.0/install.sh | \
	PROFILE=/etc/bash.bashrc bash

RUN nvm install $NODE_VERSION \
	&& nvm alias default $NODE_VERSION \
	&& nvm use default \
	&& npm install -g bower webpack

# Configure php and nginx with our basic options
RUN echo "daemon off;" >> /etc/nginx/nginx.conf; \
	sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /etc/php/5.6/fpm/php-fpm.conf; \
	sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php/5.6/fpm/php.ini; \
	sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php/5.6/fpm/php.ini; \
	sed -i "s/;date.timezone =.*/date.timezone = UTC/" /etc/php/5.6/cli/php.ini

# Remove an existing www data and prepare for our project
RUN rm -rf /var/www; \
 	mkdir -p /var/www; \
	mkdir -p /var/www/cache; \
	chown -R www-data.www-data /var/www

# Copy across our dependency definitions
ADD ./composer.* /var/www/
ADD ./package.json /var/www/
ADD ./bower.json /var/www/

# Install all our dependencies.
WORKDIR /var/www
RUN composer install --no-interaction
RUN npm install
RUN bower install --allow-root

# Copy across the Dachi tiller configuration
RUN cp -r /var/www/vendor/ouropencode/dachi/docker /etc/tiller

# Final cleanup
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Copy across the projects code
ADD ./ /var/www
