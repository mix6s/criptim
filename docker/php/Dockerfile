FROM ubuntu:16.04

RUN apt-get update \
    && apt-get install -y locales \
    && locale-gen ru_RU.utf8 en_US.UTF-8 \
    && ln -sf /usr/share/zoneinfo/Europe/Moscow /etc/localtime

ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

RUN apt-get update \
    && apt-get install -y curl zip unzip git locales software-properties-common postgresql-client \
    && add-apt-repository -y ppa:ondrej/php \
    && apt-get update \
    && apt-get install -y php7.1-fpm php7.1-cli php7.1-mcrypt php7.1-gd \
       php7.1-pgsql php-memcached php7.1-mbstring php7.1-xml php7.1-curl \
       php-xdebug php7.1-bcmath \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && mkdir /run/php \
    && apt-get remove -y --purge software-properties-common \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN usermod -u 1000 www-data

ADD php-fpm.conf /etc/php/7.1/fpm/php-fpm.conf
ADD www.conf /etc/php/7.1/fpm/pool.d/www.conf

RUN sed -i "s/;date.timezone =.*/date.timezone = Europe\/Moscow/" /etc/php/7.1/fpm/php.ini \
    && sed -i "s/;date.timezone =.*/date.timezone = Europe\/Moscow/" /etc/php/7.1/cli/php.ini \
    && sed -i "s/short_open_tag = Off/short_open_tag = On/" /etc/php/7.1/fpm/php.ini \
    && sed -i "s/short_open_tag = Off/short_open_tag = On/" /etc/php/7.1/cli/php.ini \
    && echo "xdebug.remote_enable = 1" >> /etc/php/7.1/mods-available/xdebug.ini \
    && echo "xdebug.remote_connect_back = 1" >> /etc/php/7.1/mods-available/xdebug.ini \
    && echo "xdebug.remote_port = 9900" >> /etc/php/7.1/mods-available/xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /etc/php/7.1/mods-available/xdebug.ini

RUN apt-get update \
    && apt-get install acl

COPY ./docker-entrypoint.sh /
ENTRYPOINT ["/docker-entrypoint.sh"]

WORKDIR /criptim

EXPOSE 9000
CMD ["php-fpm7.1"]
