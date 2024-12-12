#FROM gitlab.labsvr.ru:4567/corp/l-post/php8:8.1
#
#COPY php-lab.ini /etc/php/8.1/fpm/conf.d/81-php-ini-lab.ini
#COPY php-lab.ini /etc/php/8.1/cli/conf.d/81-php-ini-lab.ini
#COPY php-fpm.conf /etc/php/8.1/fpm/php-fpm.conf
#
#COPY entrypoint.sh /root/entrypoint.sh
#RUN chmod 0777 /root/entrypoint.sh
#
#RUN echo 'cd /var/www/deltabook'>>/root/.bashrc
FROM gitlab.labsvr.ru:4567/corp/l-post/php8:8.1 as builder

COPY php-lab.ini /etc/php/8.1/fpm/conf.d/81-php-ini-lab.ini
COPY php-lab.ini /etc/php/8.1/cli/conf.d/81-php-ini-lab.ini
COPY php-fpm.conf /etc/php/8.1/fpm/php-fpm.conf

FROM gitlab.labsvr.ru:4567/corp/l-post/php8:8.1

COPY --from=builder /etc/php/8.1 /etc/php/8.1
# COPY --from=builder /var/www /var/www

RUN echo 'cd /var/www/deltabook' >> /root/.bashrc

WORKDIR /var/www/deltabook/current

RUN php-fpm8.1 -D && \
    chown -R :www-data . && \
    chmod -R g+w . && \
    composer install && \
    yarn install && \
    nohup yarn run dev > /dev/null 2>&1 &

WORKDIR /var/www/deltabook/current/backend
RUN yarn install && \
    nohup yarn run dev > /dev/null 2>&1 &

WORKDIR /var/www/deltabook/current
