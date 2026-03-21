#
#   Dockerfile
#
FROM php:8.2-apache


#
#   working
#
WORKDIR /opt/spark


#
#   copy
#
COPY .env   /opt/spark/.env
COPY public /opt/spark/public


#
#   docroot
#
RUN sed -i 's,DocumentRoot /var/www/html,DocumentRoot /opt/spark/public,g' '/etc/apache2/sites-available/000-default.conf'
RUN printf '%s\n' \
    '<Directory /opt/spark/public>' \
    '    Options -Indexes -FollowSymLinks' \
    '    AllowOverride All' \
    '    Require all granted' \
    '</Directory>' \
    >> /etc/apache2/apache2.conf


#
#   expose
#
EXPOSE 80
